<?php

namespace App\Mail;

use App\Models\SecurityEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecurityAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public SecurityEvent $event;

    /**
     * Create a new message instance.
     */
    public function __construct(SecurityEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Build the message.
     */
    public function build(): SecurityAlertMail
    {
        return $this->subject('Security Alert Detected')
            ->view('emails.security_alert')
            ->with([
                'ip' => $this->event->ip,
                'attackType' => $this->event->attack_type ?? $this->event->type,
                'domain' => $this->event->domain ?? 'N/A',
                'threatLevel' => $this->event->threat_level ?? 'UNKNOWN',
                'description' => $this->event->description,
                'eventTime' => $this->event->created_at->format('Y-m-d H:i'),
            ]);
    }
}
