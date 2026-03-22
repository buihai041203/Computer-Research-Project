<?php

namespace App\Http\Middleware;

use App\Models\BlockedIP;
use App\Models\Domain;
use App\Models\TrafficLog;
use App\Services\SecurityDetector;
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

        if (BlockedIP::where('ip', $ip)->exists()) {
            abort(403, 'Your IP has been blocked');
        }

        // Bỏ qua request không cần track để tránh spam
        if (
            $request->method() !== 'GET' ||
            !$request->acceptsHtml() ||
            $request->is('dashboard*') ||
            $request->is('api/*') ||
            $request->is('domains*') ||
            $request->is('login') ||
            $request->is('register') ||
            $request->is('profile*') ||
            preg_match('/\.(css|js|map|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf|eot|txt|xml)$/i', $request->path())
        ) {
            return $next($request);
        }

        $domain = $request->getHost();

        $domainModel = Domain::where('domain', $domain)
            ->where('is_active', true)
            ->first();

        if (!$domainModel) {
            return $next($request); // host không nằm trong danh sách quản lý
        }

        $agent = $request->userAgent() ?? 'unknown';
        $type = str_contains(strtolower($agent), 'bot') ? 'bot' : 'human';

        // Dedupe trong 10 giây cho cùng IP + UA + domain
        $duplicated = TrafficLog::query()
            ->where('ip', $ip)
            ->where('domain', $domain)
            ->where('user_agent', $agent)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($duplicated) {
            return $next($request);
        }

        $country = Cache::remember("geo_country_{$ip}", now()->addHours(24), function () use ($ip) {
            try {
                $geo = Http::timeout(2)->get('http://ip-api.com/json/' . $ip)->json();
                return $geo['country'] ?? 'Unknown';
            } catch (\Throwable $e) {
                return 'Unknown';
            }
        });

        TrafficLog::create([
            'domain_id' => $domainModel->id,
            'domain' => $domain,
            'ip' => $ip,
            'user_agent' => $agent,
            'type' => $type,
            'country' => $country,
        ]);

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
            BlockedIP::firstOrCreate(
                ['ip' => $ip],
                ['reason' => 'Spam requests (' . $requests . '/min)']
            );

            $this->sendTelegramOnce(
                "attack_{$ip}",
                "⚠️ ATTACK DETECTED\nIP: {$ip}\nRequests/min: {$requests}\nAction: BLOCKED",
                300
            );
        }

        SecurityDetector::detect($ip);

        $this->sendTelegramOnce(
            "visitor_{$ip}",
            "🚨 New Visitor\nDomain: {$domain}\nIP: {$ip}\nCountry: {$country}\nType: {$type}\nDevice: {$agent}\nTime: " . now(),
            300
        );

        return $next($request);
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
