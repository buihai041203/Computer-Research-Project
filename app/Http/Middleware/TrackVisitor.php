<?php

namespace App\Http\Middleware;
use Stevebauman\Location\Facades\Location;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {

        $ip = $request->ip();
        $agent = $request->userAgent();

        $location = Location::get($ip);

        $country = "Unknown";

        if($location){
            $country = $location->countryName;
        }

        $bots = ['bot','crawl','slurp','spider'];

        $isBot = false;

        foreach($bots as $bot){
            if(stripos($agent,$bot) !== false){
                $isBot = true;
            }
        }

        Visitor::create([
            'ip' => $ip,
            'country' => $country,
            'user_agent' => $agent,
            'is_bot' => $isBot
        ]);

        return $next($request);
    }
}
