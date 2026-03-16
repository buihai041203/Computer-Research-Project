<?php

namespace App\Services;

use App\Models\TrafficLog;
use App\Models\SecurityEvent;
use App\Services\FirewallService;
use App\Jobs\SendTelegramAlert;

class SecurityDetector
{

    public static function detect($ip)
    {

        $count = TrafficLog::where('ip', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($count > 50) {

            // lưu security event
            $event = SecurityEvent::create([
                'ip' => $ip,
                'type' => 'possible_ddos',
                'description' => 'Too many requests from this IP',
                'attack_type' => 'DDoS',
                'threat_level' => 'HIGH'
            ]);

            // trigger alert for High/Critical threats (observer in AppServiceProvider handles it)
            // AlertService::sendSecurityAlert($event); // optional, event observer already does this

            // block ip
            FirewallService::block($ip, 'Too many requests');

            $message =
                "⚠️ Security Alert\n\n".
                "Possible DDoS detected\n\n".
                "IP: $ip\n\n".
                "Requests: $count\n\n".
                "Reason: Too many requests";

            // gửi telegram qua queue
            SendTelegramAlert::dispatch($message);

            return true;
        }

        return false;
    }
}
