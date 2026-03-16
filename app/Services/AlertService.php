<?php

namespace App\Services;

use App\Mail\SecurityAlertMail;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AlertService
{
    /**
     * Send security alert email to all admin users.
     */
    public static function sendSecurityAlert(SecurityEvent $event): void
    {
        $admins = User::where('role', 'admin')->whereNotNull('email')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->queue(new SecurityAlertMail($event));
        }
    }
}
