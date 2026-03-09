<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendTelegramAlert implements ShouldQueue
{

use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

public $message;

public function __construct($message)
{
$this->message = $message;
}

public function handle()
{

$botToken = config('services.telegram.token');
$chatId = config('services.telegram.chat');

Http::get("https://api.telegram.org/bot".$botToken."/sendMessage",[
'chat_id'=>$chatId,
'text'=>$this->message
]);

}

}
