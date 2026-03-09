<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{

    public static function send($message)
    {

        $token = env('TELEGRAM_BOT_TOKEN');
        $chat = env('TELEGRAM_CHAT_ID');

        Http::withoutVerifying()->post(
            "https://api.telegram.org/bot$token/sendMessage",
            [
                'chat_id'=>$chat,
                'text'=>$message
            ]
        );

    }

}
