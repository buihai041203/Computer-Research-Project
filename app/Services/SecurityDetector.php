<?php

namespace App\Services;

use App\Models\TrafficLog;
use App\Models\SecurityEvent;
use App\Services\FirewallService;
use Illuminate\Support\Facades\Http;

class SecurityDetector
{

    public static function detect($ip)
    {

        $count = TrafficLog::where('ip', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($count > 50) {

            // lưu security event
            SecurityEvent::create([
                'ip' => $ip,
                'type' => 'possible_ddos',
                'description' => 'Too many requests from this IP'
            ]);

            // block ip
            FirewallService::block($ip, 'Too many requests');

            $message =
                "⚠️ Security Alert\n\n".
                "Possible DDoS detected\n\n".
                "IP: $ip\n\n".
                "Requests: $count\n\n".
                "Reason: Too many requests";

            self::sendTelegramDirect($message);

            return true;
        }

        return false;
    }

    private static function sendTelegramDirect(string $message): void
    {
        $botToken = config('services.telegram.token');
        $chatId = config('services.telegram.chat');

        if (!$botToken || !$chatId) {
            return;
        }

        try {
            Http::timeout(10)->get('https://api.telegram.org/bot' . $botToken . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $message,
            ]);
        } catch (\Throwable $e) {
            // giữ nguyên hành vi hệ thống: không làm fail luồng detect nếu Telegram lỗi
        }
    }
}
