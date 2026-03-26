<?php

namespace App\Http\Middleware;

use App\Models\BlockedIP;
use App\Models\Domain;
use App\Models\TrafficLog;
use App\Services\FirewallService;
use App\Services\SecurityDetector;
use App\Services\ThreatAnalyzer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
        if (BlockedIP::where('ip', $ip)->exists()) {
            abort(403, 'Your IP has been blocked');
        }

        // Bỏ qua request không cần track để tránh spam
        if (
            $request->method() !== 'GET' ||
            !$request->acceptsHtml() ||
            preg_match('/\.(css|js|map|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf|eot|txt|xml)$/i', $request->path())
        ) {
            return $next($request);
        }

        $domain = $request->getHost();
        $domainModel = Domain::where('domain', $domain)->first();

        if (!$domainModel) {
            return $next($request); // host không nằm trong danh sách quản lý
        }

        // Nếu domain đang tắt thì chặn luôn request (tắt serve ở tầng ứng dụng)
        if (!($domainModel->is_active ?? true)) {
            abort(503, 'This website is temporarily disabled by Security Panel.');
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

        return $next($request);
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