<?php

namespace App\Http\Controllers;

use App\Models\BlockedIP;
use App\Models\Domain;
use App\Models\TrafficLog;
use App\Services\BlocklistSyncService;
use Illuminate\Http\Request;

class FirewallController extends Controller
{
    public function index()
    {
        $ips = BlockedIP::latest()->get();
        $domains = Domain::query()->where('is_active', true)->orderBy('domain')->get(['id', 'domain']);

        $suspicious = TrafficLog::query()
            ->selectRaw('ip, COUNT(*) as total, SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) as risky')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('ip')
            ->orderByDesc('risky')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $ipByDomain = TrafficLog::query()
            ->selectRaw('domain, ip, COUNT(*) as total, SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) as risky, MAX(created_at) as last_seen')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('domain', 'ip')
            ->orderByDesc('risky')
            ->orderByDesc('total')
            ->limit(100)
            ->get();

        $domainAttackSummary = TrafficLog::query()
            ->selectRaw('domain, COUNT(*) as total, SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) as risky, COUNT(DISTINCT ip) as attacker_ips')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('domain')
            ->orderByDesc('risky')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        return view('firewall.index', compact('ips', 'domains', 'suspicious', 'ipByDomain', 'domainAttackSummary'));
    }

    public function block(Request $request)
    {
        $data = $request->validate([
            'ip' => 'required|ip',
            'reason' => 'nullable|string|max:500',
            'scope_type' => 'nullable|in:global,domain',
            'scope_value' => 'nullable|integer',
            'ttl_minutes' => 'nullable|integer|min:0|max:10080',
        ]);

        $whitelist = array_filter(array_map('trim', explode(',', (string) env('FIREWALL_WHITELIST_IPS', '127.0.0.1,::1'))));
        if (in_array($data['ip'], $whitelist, true)) {
            return back()->with('error', 'IP này nằm trong whitelist hệ thống, không thể block.');
        }

        $scopeType = $data['scope_type'] ?? 'global';
        $scopeValue = null;

        if ($scopeType === 'domain') {
            $scopeValue = (int) ($data['scope_value'] ?? 0);
            if ($scopeValue <= 0 || !Domain::query()->where('id', $scopeValue)->exists()) {
                return back()->with('error', 'Domain scope không hợp lệ.');
            }
        }

        $ttl = (int) ($data['ttl_minutes'] ?? 0);
        $expiresAt = $ttl > 0 ? now()->addMinutes($ttl) : null;

        BlockedIP::updateOrCreate(
            [
                'ip' => $data['ip'],
                'scope_type' => $scopeType,
                'scope_value' => $scopeValue,
            ],
            [
                'reason' => $data['reason'] ?? 'Manual block from firewall panel',
                'expires_at' => $expiresAt,
                'source' => 'manual',
            ]
        );

        $sync = app(BlocklistSyncService::class)->sync();
        if (!$sync['ok']) {
            return back()->with('error', 'Đã lưu DB nhưng sync Nginx lỗi: ' . $sync['message']);
        }

        return back()->with('success', 'IP đã được block và áp dụng trên Nginx.');
    }

    public function autoBlock(Request $request)
    {
        $threshold = (int) env('TRAFFIC_BLOCK_THRESHOLD', 120);
        $highRepeat = (int) env('FIREWALL_HIGH_REPEAT', 2);
        $criticalInstant = (bool) env('FIREWALL_CRITICAL_INSTANT_BLOCK', true);
        $domainTtlMinutes = (int) env('AUTOBLOCK_DOMAIN_TTL_MINUTES', 30);
        $globalTtlMinutes = (int) env('AUTOBLOCK_GLOBAL_TTL_MINUTES', 60);
        $globalDomainCount = (int) env('AUTOBLOCK_GLOBAL_DOMAIN_COUNT', 3);

        // 1) Domain-scoped autoblock (default)
        $domainCandidates = TrafficLog::query()
            ->selectRaw('ip, domain, COUNT(*) as total,
                SUM(CASE WHEN threat = "HIGH" THEN 1 ELSE 0 END) as high_count,
                SUM(CASE WHEN threat = "CRITICAL" THEN 1 ELSE 0 END) as critical_count')
            ->where('created_at', '>=', now()->subMinute())
            ->groupBy('ip', 'domain')
            ->havingRaw(
                'COUNT(*) > ? OR SUM(CASE WHEN threat = "HIGH" THEN 1 ELSE 0 END) >= ? OR (? = 1 AND SUM(CASE WHEN threat = "CRITICAL" THEN 1 ELSE 0 END) >= 1)',
                [$threshold, $highRepeat, $criticalInstant ? 1 : 0]
            )
            ->get();

        $blockedDomain = 0;
        foreach ($domainCandidates as $c) {
            $domainId = Domain::query()->where('domain', $c->domain)->value('id');
            if (!$domainId) {
                continue;
            }

            $entry = BlockedIP::firstOrCreate(
                [
                    'ip' => $c->ip,
                    'scope_type' => 'domain',
                    'scope_value' => $domainId,
                ],
                [
                    'reason' => "Auto blocked(domain): {$c->domain}, {$c->total} req/min, high={$c->high_count}, critical={$c->critical_count}",
                    'expires_at' => now()->addMinutes($domainTtlMinutes),
                    'source' => 'auto',
                ]
            );

            if ($entry->wasRecentlyCreated) {
                $blockedDomain++;
            }
        }

        // 2) Escalate to global if same IP attacks many domains in 10 minutes
        $globalCandidates = TrafficLog::query()
            ->selectRaw('ip, COUNT(DISTINCT domain) as attacked_domains, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->groupBy('ip')
            ->havingRaw('COUNT(DISTINCT domain) >= ?', [$globalDomainCount])
            ->get();

        $blockedGlobal = 0;
        foreach ($globalCandidates as $g) {
            $entry = BlockedIP::firstOrCreate(
                [
                    'ip' => $g->ip,
                    'scope_type' => 'global',
                    'scope_value' => null,
                ],
                [
                    'reason' => "Auto blocked(global): {$g->attacked_domains} domains / 10m, total={$g->total}",
                    'expires_at' => now()->addMinutes($globalTtlMinutes),
                    'source' => 'auto',
                ]
            );

            if ($entry->wasRecentlyCreated) {
                $blockedGlobal++;
            }
        }

        $sync = app(BlocklistSyncService::class)->sync();
        if (!$sync['ok']) {
            return back()->with('error', 'Auto-block đã ghi DB nhưng sync Nginx lỗi: ' . $sync['message']);
        }

        return back()->with('success', "Auto-block hoàn tất. Domain-block mới: {$blockedDomain}, Global-block mới: {$blockedGlobal}.");
    }

    public function unblock($id)
    {
        BlockedIP::findOrFail($id)->delete();

        $sync = app(BlocklistSyncService::class)->sync();
        if (!$sync['ok']) {
            return back()->with('error', 'Đã bỏ block DB nhưng sync Nginx lỗi: ' . $sync['message']);
        }

        return back()->with('success', 'IP đã được bỏ chặn và áp dụng trên Nginx.');
    }
}
