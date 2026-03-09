<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TrafficLog;
use App\Services\SecurityDetector;
use Illuminate\Support\Facades\Cache;
use App\Models\BlockedIP;
use App\Services\ThreatAnalyzer;
use App\Models\Domain;

class LogTraffic
{

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        if(BlockedIP::where('ip',$ip)->exists()){
            abort(403,"Your IP has been blocked");
        }

        $ip = $request->ip();

        $domain = $request->getHost();

        $domainModel = Domain::firstOrCreate([
            'domain'=>$domain
        ]);

        $agent = $request->userAgent();

        $type = 'human';

        if(str_contains(strtolower($agent), 'bot')){
            $type = 'bot';
        }

        // LẤY COUNTRY
        $country = "Unknown";

        try{

            $geo = Http::get("http://ip-api.com/json/".$ip)->json();

            if(isset($geo['country'])){
                $country = $geo['country'];
            }

        }catch(\Exception $e){}

        TrafficLog::create([
            'domain_id'=>$domainModel->id,
            'domain'=>$domain,
            'ip'=>$ip,
            'user_agent'=>$agent,
            'type'=>$type,
            'country'=>$country
        ]);

        $threat = ThreatAnalyzer::analyze($ip,$type,$country);
        if($threat['level'] == 'HIGH' || $threat['level'] == 'CRITICAL'){

            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            $message = "⚠️ THREAT DETECTED\n".
            "IP: ".$ip."\n".
            "Risk Score: ".$threat['score']."\n".
            "Threat Level: ".$threat['level'];

            Http::withoutVerifying()->get(
                "https://api.telegram.org/bot".$botToken."/sendMessage",
            [
                'chat_id'=>$chatId,
                'text'=>$message
            ]);

        }

        $requests = TrafficLog::where('ip',$ip)
            ->where('created_at','>=',now()->subMinute())
            ->count();

        if($requests > 100){

            BlockedIP::create([
                'ip'=>$ip,
                'reason'=>'Spam requests'
            ]);

            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            $message = "⚠️ ATTACK DETECTED\n".
            "IP: ".$ip."\n".
            "Requests: ".$requests."\n".
            "Action: BLOCKED";

            Http::withoutVerifying()->get(
                "https://api.telegram.org/bot".$botToken."/sendMessage",
            [
                'chat_id'=>$chatId,
                'text'=>$message
            ]);

        }

        SecurityDetector::detect($ip);

        // TELEGRAM ALERT
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $key = "telegram_sent_".$ip;

        if(!Cache::has($key)){

            $message = "🚨 New Visitor\n".
            "Domain: ".$domain."\n".
            "IP: ".$ip."\n".
            "Country: ".$country."\n".
            "Type: ".$type."\n".
            "Device: ".$agent."\n".
            "Time: ".now();

            Http::withoutVerifying()->get(
                "https://api.telegram.org/bot".$botToken."/sendMessage",
                [
                    'chat_id'=>$chatId,
                    'text'=>$message
                ]
            );

            // chỉ gửi 1 lần mỗi 5 phút
            Cache::put($key, true, now()->addMinutes(5));
        }
        return $next($request);
    }
}
