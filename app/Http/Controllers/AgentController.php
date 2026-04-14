<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\Domain;
use App\Models\SecurityEvent;
use App\Models\TrafficLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            'login_fail_count' => 'nullable|integer|min:1|max:1000',
            'login_email' => 'nullable|string|max:255',
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

        $eventType = (string) $request->input('event_type', '');
        $shouldRecordSecurityEvent = ($eventType !== '' && $eventType !== 'login_success')
            || in_array($threat, ['HIGH', 'CRITICAL'], true);

        if ($shouldRecordSecurityEvent) {
            SecurityEvent::create([
                'ip' => $ip,
                'type' => $request->event_type ?? 'suspicious_activity',
                'description' => $request->event_description ?? ('Threat: ' . $threat . ' | Path: ' . ($request->path ?? '-')),
            ]);
        }

        $loginFailThreshold = (int) env('FIREWALL_LOGIN_FAIL_BLOCK_THRESHOLD', 5);
        $loginFailWindowMinutes = (int) env('FIREWALL_LOGIN_FAIL_WINDOW_MINUTES', 10);
        $loginFailTtlMinutes = (int) env('FIREWALL_LOGIN_FAIL_BLOCK_TTL_MINUTES', 30);

        if ($request->input('event_type') === 'login_success') {
            BlockedIp::query()
                ->where('ip', $ip)
                ->where('reason', 'like', 'Auto blocked(login):%')
                ->delete();
        }

        if ($request->input('event_type') === 'login_failed') {
            $recentFails = SecurityEvent::query()
                ->where('ip', $ip)
                ->where('type', 'login_failed')
                ->where('created_at', '>=', now()->subMinutes($loginFailWindowMinutes))
                ->count();

            if (!$isSystemIp && $recentFails >= $loginFailThreshold) {
                BlockedIp::updateOrCreate(
                    [
                        'ip' => $ip,
                        'scope_type' => 'domain',
                        'scope_value' => $domain->id,
                    ],
                    [
                        'reason' => "Auto blocked(login): {$domain->domain}, failed login {$recentFails} times / {$loginFailWindowMinutes}m",
                        'expires_at' => now()->addMinutes($loginFailTtlMinutes),
                        'source' => 'auto',
                    ]
                );

                $this->sendTelegramDirect(
                    "🚫 LOGIN AUTOBLOCK\n" .
                    "Domain: {$domain->domain}\n" .
                    "IP: {$ip}\n" .
                    "Failed login attempts: {$recentFails}\n" .
                    "Window: {$loginFailWindowMinutes} minutes\n" .
                    "Block TTL: {$loginFailTtlMinutes} minutes"
                );
            }
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
        $isLoginSignal = in_array($eventType, ['login_failed', 'login_success'], true);

        if (!$isLoginSignal) {
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
        }

        if (!$isSystemIp && $shouldBlock) {
            BlockedIp::updateOrCreate(
                [
                    'ip' => $ip,
                    'scope_type' => 'global',
                    'scope_value' => null,
                ],
                [
                    'reason' => $reason,
                    'source' => 'auto',
                ]
            );

            $this->sendTelegramDirect(
                "⚠️ ATTACK DETECTED\n" .
                "Domain: {$domain->domain}\n" .
                "IP: {$ip}\n" .
                "Threat: {$threat}\n" .
                "Reason: {$reason}"
            );
        }

        app(\App\Services\BlocklistSyncService::class)->sync();

        if ($eventType !== 'login_success' && in_array($threat, ['HIGH', 'CRITICAL'], true) && $request->filled('event_type')) {
            $this->sendTelegramDirect(
                "⚠️ SECURITY EVENT\n" .
                "Domain: {$domain->domain}\n" .
                "IP: {$ip}\n" .
                "Type: " . ($request->input('event_type') ?? 'suspicious_activity') . "\n" .
                "Threat: {$threat}\n" .
                "Description: " . ($request->input('event_description') ?? '-')
            );
        }

        return response()->json([
            'status' => 'ok',
            'blocked' => BlockedIp::where('ip', $ip)->where(function ($q) use ($domain) {
                $q->where('scope_type', 'global')
                    ->orWhere(function ($sub) use ($domain) {
                        $sub->where('scope_type', 'domain')->where('scope_value', $domain->id);
                    });
            })->exists(),
        ]);
    }

    private function sendTelegramDirect(string $message): void
    {
        $botToken = config('services.telegram.token');
        $chatId = config('services.telegram.chat');

        if (!$botToken || !$chatId) {
            return;
        }

        try {
            Http::timeout(10)->get('https://api.telegram.org/bot' . $botToken . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $message,
            ]);
        } catch (\Throwable $e) {
            // không làm fail luồng collect nếu Telegram lỗi
        }
    }
}
