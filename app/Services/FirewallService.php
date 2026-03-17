<?php

namespace App\Services;

use App\Models\BlockedIp;

use Symfony\Component\Process\Process;

class FirewallService
{

    public static function block(string $ip, string $reason): void
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return;
        }

        BlockedIp::firstOrCreate([
            'ip' => $ip
        ], [
            'reason' => $reason,
            'blocked_at' => now()
        ]);

        if (config('app.enable_firewall_commands', false)) {
            $process = new Process(['sudo', 'ufw', 'deny', 'from', $ip]);
            $process->setTimeout(10);

            try {
                $process->mustRun();
            } catch (\Exception $e) {
                // log failure; do not break request
                logger()->error('FirewallService::block failed', [
                    'ip' => $ip,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

}
