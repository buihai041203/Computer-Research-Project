<?php

namespace App\Services;

use App\Models\BlockedIp;

class FirewallService
{

    public static function block($ip,$reason)
    {

        BlockedIp::create([
            'ip'=>$ip,
            'reason'=>$reason,
            'blocked_at'=>now()
        ]);

        // block bằng firewall
        exec("sudo ufw deny from ".$ip);

    }

}
