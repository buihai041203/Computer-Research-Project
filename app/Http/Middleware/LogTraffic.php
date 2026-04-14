<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use App\Models\Domain;
use App\Models\TrafficLog;
use App\Services\FirewallService;
use App\Services\SecurityDetector;
use App\Services\ThreatAnalyzer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;
use App\Http\Controllers\DomainController; // 👉 ĐÃ THÊM: Import Controller để gọi lệnh Nginx

class LogTraffic
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Panel routes luôn được đi qua để tránh tự khóa admin
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

        // Never block local/system/whitelisted IPs
        if ($this->isSystemIp($ip)) {
            return $next($request);
        }

        // Bị block thì chặn ở lớp ứng dụng (áp cho sites)
        if (BlockedIp::where('ip', $ip)->exists()) {
            abort(403, 'Your IP has been blocked');
        }

        $domain = $request->getHost();
        $domainModel = Domain::where('domain', $domain)->first();

        $requestPath = '/' . ltrim($request->path(), '/');
        $isLoginPath = $requestPath === '/lms/register/login.php' || $requestPath === '/lms/login.php';
        $isLoginAttempt = $isLoginPath && strtoupper($request->method()) === 'POST';

        // Bỏ qua request không cần track để tránh spam, nhưng vẫn cho phép luồng login POST của LMS đi qua để autoblock
        if (
            (!$isLoginAttempt && $request->method() !== 'GET') ||
            (!$isLoginAttempt && !$request->acceptsHtml()) ||
            preg_match('/\.(css|js|map|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf|eot|txt|xml)$/i', $request->path())
        ) {
            return $next($request);
        }

        if (!$domainModel) {
            return $next($request); // host không nằm trong danh sách quản lý
        }

        // Nếu domain đang tắt thì chặn luôn request (tắt serve ở tầng ứng dụng)
        if (!($domainModel->is_active ?? true)) {
            abort(503, 'This website is temporarily disabled by Security Panel.');
        }

        $agent = $request->userAgent() ?? 'unknown';
        $type = str_contains(strtolower($agent), 'bot') ? 'bot' : 'human';

        // Dedupe trong 10 giây cho cùng IP + UA + domain (không áp dụng cho login POST để đếm đủ số lần sai)
        $duplicated = !$isLoginAttempt && TrafficLog::query()
            ->where('ip', $ip)
            ->where('domain', $domain)
            ->where('user_agent', $agent)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($duplicated) {
            return $next($request);
        }

        $countryCacheKey = "geo_country_{$ip}";
        $country = Cache::get($countryCacheKey);

        if (!$country || $country === 'Unknown') {
            $knownCountry = TrafficLog::query()
                ->where('ip', $ip)
                ->whereNotNull('country')
                ->where('country', '!=', 'Unknown')
                ->latest()
                ->value('country');

            if ($knownCountry) {
                $country = $knownCountry;
                Cache::put($countryCacheKey, $country, now()->addHours(24));
            } else {
                $resolvedCountry = $this->resolveCountry($ip);
                if ($resolvedCountry !== 'Unknown') {
                    $country = $resolvedCountry;
                    Cache::put($countryCacheKey, $country, now()->addHours(24));
                } else {
                    $country = 'Unknown';
                    Cache::put($countryCacheKey, $country, now()->addSeconds(2));
                }
            }
        }

        $response = $next($request);
        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;

        $trafficLog = TrafficLog::create([
            'domain_id' => $domainModel->id,
            'domain' => $domain,
            'request_path' => $requestPath,
            'status_code' => $statusCode,
            'ip' => $ip,
            'user_agent' => $agent,
            'type' => $type,
            'country' => $country,
        ]);

        if ($country === 'Unknown') {
            $this->attemptImmediateCountryCorrection($trafficLog, $ip);
        }

        $this->handleLoginAutoblock($domainModel->id, $domain, $ip, $requestPath, $isLoginAttempt, $response);

        $threat = ThreatAnalyzer::analyze($ip, $type, $country);
        if (($threat['level'] ?? 'LOW') === 'HIGH' || ($threat['level'] ?? 'LOW') === 'CRITICAL') {
            $this->sendTelegramOnce(
                "threat_{$ip}",
                "⚠️ THREAT DETECTED\nIP: {$ip}\nRisk Score: " . ($threat['score'] ?? 0) . "\nThreat Level: " . ($threat['level'] ?? 'LOW'),
                120
            );
        }

        $requests = TrafficLog::where('ip', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $threshold = (int) env('TRAFFIC_BLOCK_THRESHOLD', 120);
        
        if ($requests > $threshold) {
            $reason = "Auto blocked: {$requests} req/min";
            FirewallService::block($ip, $reason);

            // Auto-disable domain nếu bị tấn công liên tục
            $windowSeconds = (int) env('AUTO_DISABLE_DOMAIN_WINDOW_SECONDS', 600);
            $afterBlocks = (int) env('AUTO_DISABLE_DOMAIN_AFTER_BLOCKS', 3);
            $hitsKey = "domain_attack_hits:{$domainModel->id}";
            $hits = (int) Cache::get($hitsKey, 0) + 1;
            Cache::put($hitsKey, $hits, now()->addSeconds($windowSeconds));

            // 👉 ĐÃ NÂNG CẤP: Gọi Lệnh Giật Sập Nginx tại đây
            if (($domainModel->is_active ?? true) && $hits >= $afterBlocks) {
                
                // 1. GỌI HÀM EMERGENCY SHUTDOWN TỪ DOMAIN CONTROLLER (SẬP NGINX THẬT)
                DomainController::emergencyShutdown($domainModel->domain);

                // 2. Bắn thông báo Telegram khẩn cấp
                $this->sendTelegramOnce(
                    "domain_disabled_{$domainModel->id}",
                    "🚨 NGINX AUTO-OFF KÍCH HOẠT\nDomain: {$domainModel->domain}\nHits: {$hits}/{$afterBlocks}\nReason: Đã cắt đứt Nginx để chống DDoS!",
                    600
                );
            }

            $this->sendTelegramOnce(
                "attack_{$ip}",
                "⚠️ ATTACK DETECTED\nIP: {$ip}\nRequests/min: {$requests}\nAction: BLOCKED IP",
                300
            );
        }

        SecurityDetector::detect($ip);

        $this->sendTelegramOnce(
            "visitor_{$ip}",
            "🚨 New Visitor\nDomain: {$domain}\nIP: {$ip}\nCountry: {$country}\nType: {$type}\nDevice: {$agent}\nTime: " . now(),
            300
        );

        return $response;
    }

    private function resolveCountry(string $ip): string
    {
        try {
            $position = Location::get($ip);
            if ($position && !empty($position->countryName)) {
                return (string) $position->countryName;
            }
        } catch (\Throwable $e) {
            // fall through to direct providers below
        }

        try {
            $geo = Http::timeout(2)->get('http://ip-api.com/json/' . $ip)->json();
            if (($geo['status'] ?? null) === 'success' && !empty($geo['country'])) {
                return (string) $geo['country'];
            }
        } catch (\Throwable $e) {
            // fall through to secondary provider
        }

        try {
            $geo = Http::timeout(2)->get('https://ipinfo.io/' . $ip . '/json')->json();
            if (!empty($geo['country'])) {
                return match (strtoupper((string) $geo['country'])) {
                    'VN' => 'Vietnam',
                    'US' => 'United States',
                    'CN' => 'China',
                    'SG' => 'Singapore',
                    'RU' => 'Russia',
                    'DE' => 'Germany',
                    'FR' => 'France',
                    'JP' => 'Japan',
                    'KR' => 'South Korea',
                    'IN' => 'India',
                    'BR' => 'Brazil',
                    'GB' => 'United Kingdom',
                    'NL' => 'Netherlands',
                    'CA' => 'Canada',
                    'AU' => 'Australia',
                    default => strtoupper((string) $geo['country']),
                };
            }
        } catch (\Throwable $e) {
            // ignore and return Unknown below
        }

        return 'Unknown';
    }

    private function attemptImmediateCountryCorrection(TrafficLog $trafficLog, string $ip): void
    {
        $retryLockKey = "geo_country_retry_lock_{$ip}";

        if (!Cache::add($retryLockKey, true, now()->addSeconds(2))) {
            return;
        }

        $resolvedCountry = $this->resolveCountry($ip);
        if ($resolvedCountry === 'Unknown') {
            return;
        }

        $trafficLog->update(['country' => $resolvedCountry]);
        Cache::put("geo_country_{$ip}", $resolvedCountry, now()->addHours(24));

        TrafficLog::query()
            ->where('ip', $ip)
            ->where('country', 'Unknown')
            ->latest()
            ->limit(20)
            ->update(['country' => $resolvedCountry]);
    }

    private function handleLoginAutoblock(int $domainId, string $domain, string $ip, string $requestPath, bool $isLoginAttempt, mixed $response): void
    {
        if (!$isLoginAttempt) {
            return;
        }

        $location = method_exists($response, 'headers') ? $response->headers->get('Location', '') : '';
        $isSuccessRedirect = str_contains($location, '/lms/main/')
            || str_contains($location, '/' . $domain . '/main/')
            || str_contains($location, '/' . $domain . '/register/manager.php');

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
            ['ip' => $ip],
            ['reason' => "Auto blocked(login): {$domain}, failed login {$failCount} times / {$windowMinutes}m"]
        );

        $this->sendTelegramOnce(
            "login_block_{$domainId}_{$ip}",
            "🚫 LOGIN AUTOBLOCK\nDomain: {$domain}\nIP: {$ip}\nFailed login attempts: {$failCount}\nWindow: {$windowMinutes} minutes\nBlock TTL: {$ttlMinutes} minutes",
            120
        );
    }

    private function isSystemIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return true;
        }

        // private ranges + server public IPs (from env)
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

        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$botToken || !$chatId) {
            return;
        }

        try {
            Http::withoutVerifying()->timeout(3)->get(
                'https://api.telegram.org/bot' . $botToken . '/sendMessage',
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]
            );
            Cache::put($cacheKey, true, now()->addSeconds($ttlSeconds));
        } catch (\Throwable $e) {
            // ignore notify errors
        }
    }
}