<?php

namespace App\Services;

use App\Models\TrafficLog;

class ThreatAnalyzer
{

    public static function analyze($ip,$type,$country)
    {

        $score = 0;

        $requests = TrafficLog::where('ip',$ip)
            ->where('created_at','>=',now()->subMinute())
            ->count();

        if($requests > 50){
            $score += 40;
        }

        if($type == 'bot'){
            $score += 30;
        }

        if($country == 'Unknown'){
            $score += 10;
        }

        if($score >= 80){
            $level = "CRITICAL";
        }
        elseif($score >= 60){
            $level = "HIGH";
        }
        elseif($score >= 30){
            $level = "MEDIUM";
        }
        else{
            $level = "LOW";
        }

        return [
            'score'=>$score,
            'level'=>$level
        ];

    }

}
