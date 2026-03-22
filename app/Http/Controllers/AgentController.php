<?php

namespace App\Http\Controllers;

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

        $ua = $request->user_agent ?? 'unknown';
        $type = $request->type ?? (str_contains(strtolower($ua), 'bot') ? 'bot' : 'human');
        $threat = $request->threat ?? 'LOW';

        TrafficLog::create([
            'domain_id' => $domain->id,
            'domain' => $domain->domain,
            'ip' => $request->ip,
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

        if ($request->filled('event_type') || in_array($threat, ['HIGH', 'CRITICAL'])) {
            SecurityEvent::create([
                'ip' => $request->ip,
                'type' => $request->event_type ?? 'suspicious_activity',
                'description' => $request->event_description ?? ('Threat: ' . $threat . ' | Path: ' . ($request->path ?? '-')),
            ]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
