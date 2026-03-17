<?php

namespace App\Jobs;

use App\Models\SecurityEvent;
use App\Services\AlertService;
use App\Services\TelegramService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $securityEventId;

    public function __construct(int $securityEventId)
    {
        $this->securityEventId = $securityEventId;
    }

    public function handle(): void
    {
        $event = SecurityEvent::find($this->securityEventId);

        if (! $event) {
            return;
        }

        AlertService::sendSecurityAlert($event);

        $message = "⚠️ Security alert for domain: {$event->domain}\n" .
            "IP: {$event->ip}\n" .
            "Type: {$event->attack_type}\n" .
            "Threat: {$event->threat_level}\n" .
            "Details: {$event->description}";

        TelegramService::send($message);
    }
}
