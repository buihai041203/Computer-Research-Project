<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramService
{
    public static function send(string $message): array
    {
        $token = config('services.telegram.token');
        $chat = config('services.telegram.chat');

        if (!$token || !$chat) {
            return ['ok' => false, 'message' => 'Telegram token/chat id is missing'];
        }

        $response = Http::withoutVerifying()
            ->timeout(5)
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chat,
                'text' => $message,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Telegram send failed: ' . $response->body());
        }

        return ['ok' => true, 'message' => 'sent', 'data' => $response->json()];
    }
}
