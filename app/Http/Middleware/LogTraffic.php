    <?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\BlockedIP;
use App\Models\Domain;
use App\Models\TrafficLog;
use App\Models\SecurityEvent;
use App\Services\ThreatAnalyzer;
use App\Services\SecurityDetector;
use App\Services\FirewallService;
use App\Services\AISecurityAnalyzer;
use App\Services\AlertService;

class LogTraffic
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Kiểm tra IP bị block
        if (BlockedIP::where('ip', $ip)->exists()) {
            abort(403, "Your IP has been blocked");
        }

        $domain = $request->getHost();

        // Tạo domain nếu chưa tồn tại
        $domainModel = Domain::firstOrCreate(
            ['domain' => $domain],
            ['agent_key' => Str::random(32)]
        );

        $agent = $request->userAgent() ?? 'Unknown';

        // Xác định bot hay human
        $type = 'human';
        if ($agent && str_contains(strtolower($agent), 'bot')) {
            $type = 'bot';
        }

        // Lấy country
        $country = "Unknown";

        try {
            $geo = Http::timeout(2)->get("http://ip-api.com/json/" . $ip)->json();

            if (isset($geo['country'])) {
                $country = $geo['country'];
            }
        } catch (\Exception $e) {
            // bỏ qua lỗi API
        }

        // Lưu log
        TrafficLog::create([
            'domain_id' => $domainModel->id,
            'domain' => $domain,
            'ip' => $ip,
            'user_agent' => $agent,
            'type' => $type,
            'country' => $country
        ]);

        // AI threat analysis
        $requestFrequency = TrafficLog::where('ip', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $path = $request->path();

        $aiResult = AISecurityAnalyzer::analyze($ip, $agent, $requestFrequency, $path);

        if (in_array($aiResult['threat_level'], ['HIGH', 'CRITICAL'])) {
            $event = SecurityEvent::create([
                'ip' => $ip,
                'domain' => $domain,
                'type' => $aiResult['attack_type'],
                'description' => $aiResult['explanation'],
                'attack_type' => $aiResult['attack_type'],
                'threat_level' => $aiResult['threat_level'],
                'ai_analysis' => json_encode($aiResult['metadata'])
            ]);

            // Trigger admin email alert for high/critical event.
            AlertService::sendSecurityAlert($event);

            // Optional block for severe threats
            if ($aiResult['threat_level'] === 'CRITICAL') {
                FirewallService::block($ip, 'AI detected threat: ' . $aiResult['attack_type']);
            }

            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if ($botToken && $chatId) {

                $message = "⚠️ THREAT DETECTED\n" .
                    "IP: " . $ip . "\n" .
                    "Risk Score: " . ($aiResult['score'] ?? 'N/A') . "\n" .
                    "Threat Level: " . ($aiResult['threat_level'] ?? 'UNKNOWN');

                Http::withoutVerifying()->get(
                    "https://api.telegram.org/bot{$botToken}/sendMessage",
                    [
                        'chat_id' => $chatId,
                        'text' => $message
                    ]
                );
            }
        }

        // Kiểm tra spam request
        $requests = TrafficLog::where('ip', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($requests > 100) {

            BlockedIP::firstOrCreate([
                'ip' => $ip
            ], [
                'reason' => 'Spam requests'
            ]);

            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if ($botToken && $chatId) {

                $message = "⚠️ ATTACK DETECTED\n" .
                    "IP: " . $ip . "\n" .
                    "Requests: " . $requests . "\n" .
                    "Action: BLOCKED";

                Http::withoutVerifying()->get(
                    "https://api.telegram.org/bot{$botToken}/sendMessage",
                    [
                        'chat_id' => $chatId,
                        'text' => $message
                    ]
                );
            }
        }

        // Security scan
        SecurityDetector::detect($ip);

        // Telegram alert visitor (chỉ 1 lần mỗi 5 phút)
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $key = "telegram_sent_" . $ip;

        if (!Cache::has($key) && $botToken && $chatId) {

            $message = "🚨 New Visitor\n" .
                "Domain: " . $domain . "\n" .
                "IP: " . $ip . "\n" .
                "Country: " . $country . "\n" .
                "Type: " . $type . "\n" .
                "Device: " . $agent . "\n" .
                "Time: " . now();

            Http::withoutVerifying()->get(
                "https://api.telegram.org/bot{$botToken}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message
                ]
            );

            Cache::put($key, true, now()->addMinutes(5));
        }

        return $next($request);
    }
}