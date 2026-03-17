<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\BlockedIP;
use App\Jobs\LogTrafficJob;

class LogTraffic
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        try {
            $blockedIPs = Cache::remember('blocked_ips_list', 60, function () {
                return BlockedIP::pluck('ip')->toArray();
            });
        } catch (\Exception $e) {
            logger()->warning('LogTraffic middleware unable to query blocked IPs', ['error' => $e->getMessage()]);
            $blockedIPs = [];
        }

        if (in_array($ip, $blockedIPs, true)) {
            abort(403, 'Your IP has been blocked');
        }

        $payload = [
            'domain' => $request->getHost(),
            'ip' => $ip,
            'user_agent' => $request->userAgent() ?? 'Unknown',
            'path' => $request->path(),
            'timestamp' => now()->toDateTimeString(),
        ];

        try {
            LogTrafficJob::dispatch($payload);
        } catch (\Exception $e) {
            logger()->warning('LogTraffic middleware queue dispatch failed', ['error' => $e->getMessage()]);
            // If queue database is not available, skip async log traffic in this request.
        }

        return $next($request);
    }
}
