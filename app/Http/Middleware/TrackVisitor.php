<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // Chỉ track request web chính, tránh spam từ assets/API/background calls
        if (
            $request->method() !== 'GET' ||
            !$request->acceptsHtml() ||
            $request->is('dashboard*') ||
            $request->is('api/*') ||
            $request->is('domains*') ||
            $request->is('login') ||
            $request->is('register') ||
            $request->is('profile*') ||
            preg_match('/\.(css|js|map|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf|eot|txt|xml)$/i', $request->path())
        ) {
            return $next($request);
        }

        $ip = $request->ip();
        $agent = $request->userAgent() ?? 'unknown';

        $bots = ['bot', 'crawl', 'slurp', 'spider'];
        $isBot = false;
        foreach ($bots as $bot) {
            if (stripos($agent, $bot) !== false) {
                $isBot = true;
                break;
            }
        }

        // Dedupe: cùng IP + UserAgent trong 20 giây thì bỏ qua
        $exists = Visitor::query()
            ->where('ip', $ip)
            ->where('user_agent', $agent)
            ->where('is_bot', $isBot)
            ->where('created_at', '>=', now()->subSeconds(20))
            ->exists();

        if (!$exists) {
            $country = 'Unknown';
            $location = Location::get($ip);
            if ($location && !empty($location->countryName)) {
                $country = $location->countryName;
            }

            Visitor::create([
                'ip' => $ip,
                'country' => $country,
                'user_agent' => $agent,
                'is_bot' => $isBot,
            ]);
        }

        return $next($request);
    }
}
