<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use App\Models\Domain;
use App\Models\TrafficLog;
use App\Services\TelegramService;
use App\Services\ThreatAnalyzer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LogTraffic
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        if (
            $request->is('dashboard*') ||
            $request->is('api/*') ||
            $request->is('domains*') ||
            $request->is('login') ||
            $request->is('register') ||
            $request->is('profile*') ||
            $request->is('firewall*') ||
            $request->is('logs*') ||
            $request->is('traffic*') ||
            $request->is('security*') ||
            $request->is('databases*')
        ) {
            return $next($request);
        }

        if ($this->isSystemIp($ip)) {
            return $next($request);
        }

        $host = $request->getHost();
        $requestPath = '/' . ltrim($request->path(), '/');

        $domainModel = Domain::query()->where('domain', $host)->first();
        if (!$domainModel) {
            $segments = array_values(array_filter(explode('/', trim($requestPath, '/'))));
            $siteSlug = $segments[0] ?? null;
            if ($siteSlug) {
                $domainModel = Domain::query()->where('domain', $siteSlug)->first();
            }
        }

        $domainId = $domainModel?->id;

        $isBlocked = BlockedIp::query()
            ->where('ip', $ip)
            ->where(function ($q) use ($domainId) {
                $q->where('scope_type', 'global')
                    ->orWhere(function ($dq) use ($domainId) {
                        $dq->where('scope_type', 'domain')
                            ->where('scope_value', $domainId ?: -1);
                    });
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($isBlocked) {
            abort(403, 'Your IP has been blocked');
        }

        if (
            !$request->acceptsHtml() ||
            preg_match('/\.(css|js|map|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf|eot|txt|xml)$/i', $request->path())
        ) {
            return $next($request);
        }

        if (!$domainModel) {
            return $next($request);
        }

        if (!($domainModel->is_active ?? true)) {
            abort(503, 'This website is temporarily disabled by Security Panel.');
        }

        $domain = $domainModel->domain;
        $agent = $request->userAgent() ?? 'unknown';
        $type = str_contains(strtolower($agent), 'bot') ? 'bot' : 'human';

        $country = Cache::remember("geo_country_{$ip}", now()->addHours(24), function () use ($ip) {
            try {
                $geo = Http::timeout(2)->get('http://ip-api.com/json/' . $ip)->json();
                return $geo['country'] ?? 'Unknown';
            } catch (\Throwable $e) {
                return 'Unknown';
            }
        });

        $response = $next($request);
        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;

        $isLoginPath = preg_match('#^/' . preg_quote($domain, '#') . '/register/login\.php$#', $requestPath)
            || preg_match('#^/' . preg_quote($domain, '#') . '/login\.php$#', $requestPath);
        $isLoginAttempt = $isLoginPath && strtoupper($request->method()) === 'POST';

        $duplicated = !($isLoginPath && $isLoginAttempt) && TrafficLog::query()
            ->where('ip', $ip)
            ->where('domain', $domain)
            ->where('request_path', $requestPath)
            ->where('status_code', $statusCode)
            ->where('user_agent', $agent)
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if (!$duplicated) {
            $threat = ThreatAnalyzer::analyze($ip, $type, $country);

            TrafficLog::create([
                'domain_id' => $domainModel->id,
                'domain' => $domain,
                'request_path' => $requestPath,
                'status_code' => $statusCode,
                'ip' => $ip,
                'user_agent' => $agent,
                'type' => $type,
                'country' => $country,
                'threat' => $threat['level'] ?? 'LOW',
                'source' => 'live',
            ]);

            $this->handleLoginAutoblock($domainModel->id, $domain, $ip, $requestPath, $isLoginAttempt, $response);
        }

        return $response;
    }

    private function handleLoginAutoblock(int $domainId, string $domain, string $ip, string $requestPath, bool $isLoginAttempt, mixed $response): void
    {
        if (!$isLoginAttempt) {
            return;
        }

        $location = $response->headers->get('Location', '');
        $isSuccessRedirect = str_contains($location, '/' . $domain . '/register/manager.php')
            || str_contains($location, '/' . $domain . '/main/');

        if ($isSuccessRedirect) {
            return;
        }

        $maxAttempts = (int) env('FIREWALL_LOGIN_FAIL_BLOCK_THRESHOLD', 5);
        $windowMinutes = (int) env('FIREWALL_LOGIN_FAIL_WINDOW_MINUTES', 10);
        $ttlMinutes = (int) env('FIREWALL_LOGIN_FAIL_BLOCK_TTL_MINUTES', 5);

        $failCount = TrafficLog::query()
            ->where('ip', $ip)
            ->where('domain_id', $domainId)
            ->where('request_path', $requestPath)
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->count();

        if ($failCount < $maxAttempts) {
            return;
        }

        BlockedIp::updateOrCreate(
            [
                'ip' => $ip,
                'scope_type' => 'domain',
                'scope_value' => $domainId,
            ],
            [
                'reason' => "Auto blocked(domain): {$domain}, failed login {$failCount} times / {$windowMinutes}m",
                'source' => 'auto',
                'expires_at' => now()->addMinutes($ttlMinutes),
            ]
        );

        $this->sendTelegramOnce(
            "login_block_{$domainId}_{$ip}",
            "🚫 LOGIN AUTOBLOCK\nDomain: {$domain}\nIP: {$ip}\nFailed login attempts: {$failCount}\nWindow: {$windowMinutes} minutes\nBlock TTL: {$ttlMinutes} minutes",
            120
        );
    }

    private function isSystemIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1'], true)) {
            return true;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }

        $whitelist = array_filter(array_map('trim', explode(',', (string) env('FIREWALL_WHITELIST_IPS', ''))));
        return in_array($ip, $whitelist, true);
    }

    private function sendTelegramOnce(string $cacheKey, string $message, int $ttlSeconds = 300): void
    {
        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            $result = TelegramService::send($message);
            if (($result['ok'] ?? false) === true) {
                Cache::put($cacheKey, true, now()->addSeconds($ttlSeconds));
            }
        } catch (\Throwable $e) {
            // ignore notify errors
        }
    }
}
