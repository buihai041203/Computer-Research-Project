<?php

namespace App\Http\Controllers;

use App\Models\BlockedIP;
use App\Models\Domain;
use App\Models\SecurityEvent;
use App\Models\TrafficLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    public function collect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'ip' => 'required|ip',
            'path' => 'nullable|string|max:1024',
            'method' => 'nullable|string|max:10',
            'user_agent' => 'nullable|string|max:1000',
            'country' => 'nullable|string|max:120',
            'type' => 'nullable|in:human,bot',
            'threat' => 'nullable|in:LOW,MEDIUM,HIGH,CRITICAL',
            'event_type' => 'nullable|string|max:100',
            'event_description' => 'nullable|string|max:1000',
            'occurred_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'invalid_payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        $domain = Domain::where('domain', $request->domain)
            ->where('agent_key', $request->key)
            ->where('is_active', true)
            ->first();

        if (!$domain) {
            return response()->json([
                'status' => 'invalid_key',
            ], 403);
        }

        $ip = $request->ip;
        $whitelist = array_filter(array_map('trim', explode(',', (string) env('FIREWALL_WHITELIST_IPS', ''))));
        $isSystemIp = in_array($ip, ['127.0.0.1', '::1'], true)
            || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            || in_array($ip, $whitelist, true);

        $ua = $request->user_agent ?? 'unknown';
        $type = $request->type ?? (str_contains(strtolower($ua), 'bot') ? 'bot' : 'human');
        $threat = $request->threat ?? 'LOW';

        TrafficLog::create([
            'domain_id' => $domain->id,
            'domain' => $domain->domain,
            'ip' => $ip,
            'user_agent' => $ua,
            'type' => $type,
            'country' => $request->country ?? 'Unknown',
            'threat' => $threat,
            'session_id' => $request->header('X-Session-ID'),
            'browser' => substr($ua, 0, 255),
            'device' => $request->header('X-Device', 'web'),
            'created_at' => $request->occurred_at ?? now(),
            'updated_at' => now(),
        ]);

        if ($request->filled('event_type') || in_array($threat, ['HIGH', 'CRITICAL'], true)) {
            SecurityEvent::create([
                'ip' => $ip,
                'type' => $request->event_type ?? 'suspicious_activity',
                'description' => $request->event_description ?? ('Threat: ' . $threat . ' | Path: ' . ($request->path ?? '-')),
            ]);
        }

        $threshold = (int) env('TRAFFIC_BLOCK_THRESHOLD', 120);
        $highRepeat = (int) env('FIREWALL_HIGH_REPEAT', 2);
        $criticalInstant = (bool) env('FIREWALL_CRITICAL_INSTANT_BLOCK', true);

        $reqPerMin = TrafficLog::where('ip', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $highThreatPerMin = TrafficLog::where('ip', $ip)
            ->where('threat', 'HIGH')
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $criticalPerMin = TrafficLog::where('ip', $ip)
            ->where('threat', 'CRITICAL')
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $shouldBlock = false;
        $reason = null;

        if ($reqPerMin > $threshold) {
            $shouldBlock = true;
            $reason = "Auto blocked: {$reqPerMin} req/min > threshold {$threshold}";
        } elseif ($criticalInstant && $criticalPerMin >= 1) {
            $shouldBlock = true;
            $reason = "Auto blocked: CRITICAL threat detected";
        } elseif ($highThreatPerMin >= $highRepeat) {
            $shouldBlock = true;
            $reason = "Auto blocked: HIGH threat repeated {$highThreatPerMin}/min";
        }

        if (!$isSystemIp && $shouldBlock) {
            BlockedIP::firstOrCreate(
                ['ip' => $ip],
                ['reason' => $reason]
            );
        }

        return response()->json([
            'status' => 'ok',
            'blocked' => BlockedIP::where('ip', $ip)->exists(),
        ]);
    }
}
