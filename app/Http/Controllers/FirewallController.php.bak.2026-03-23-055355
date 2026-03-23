<?php

namespace App\Http\Controllers;

use App\Models\BlockedIP;
use App\Models\TrafficLog;
use Illuminate\Http\Request;

class FirewallController extends Controller
{
    public function index()
    {
        $ips = BlockedIP::latest()->get();

        $suspicious = TrafficLog::query()
            ->selectRaw('ip, COUNT(*) as total, SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) as risky')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('ip')
            ->orderByDesc('risky')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // IP nào đang tấn công web nào (multi-site view)
        $ipByDomain = TrafficLog::query()
            ->selectRaw('domain, ip, COUNT(*) as total, SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) as risky, MAX(created_at) as last_seen')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('domain', 'ip')
            ->orderByDesc('risky')
            ->orderByDesc('total')
            ->limit(100)
            ->get();

        // Website nào đang bị tấn công nhiều nhất
        $domainAttackSummary = TrafficLog::query()
            ->selectRaw('domain, COUNT(*) as total, SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) as risky, COUNT(DISTINCT ip) as attacker_ips')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('domain')
            ->orderByDesc('risky')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        return view('firewall.index', compact('ips', 'suspicious', 'ipByDomain', 'domainAttackSummary'));
    }

    public function block(Request $request)
    {
        $data = $request->validate([
            'ip' => 'required|ip',
            'reason' => 'nullable|string|max:500',
        ]);

        $whitelist = array_filter(array_map('trim', explode(',', (string) env('FIREWALL_WHITELIST_IPS', '127.0.0.1,::1'))));
        if (in_array($data['ip'], $whitelist, true)) {
            return back()->with('error', 'IP này nằm trong whitelist hệ thống, không thể block.');
        }

        BlockedIP::firstOrCreate(
            ['ip' => $data['ip']],
            ['reason' => $data['reason'] ?? 'Manual block from firewall panel']
        );

        return back()->with('success', 'IP đã được block.');
    }

    public function autoBlock(Request $request)
    {
        $threshold = (int) env('TRAFFIC_BLOCK_THRESHOLD', 120);
        $highRepeat = (int) env('FIREWALL_HIGH_REPEAT', 2);
        $criticalInstant = (bool) env('FIREWALL_CRITICAL_INSTANT_BLOCK', true);

        $candidates = TrafficLog::query()
            ->selectRaw('ip, COUNT(*) as total,
                SUM(CASE WHEN threat = "HIGH" THEN 1 ELSE 0 END) as high_count,
                SUM(CASE WHEN threat = "CRITICAL" THEN 1 ELSE 0 END) as critical_count')
            ->where('created_at', '>=', now()->subMinute())
            ->groupBy('ip')
            ->havingRaw('COUNT(*) > ? OR SUM(CASE WHEN threat = "HIGH" THEN 1 ELSE 0 END) >= ? OR (? = 1 AND SUM(CASE WHEN threat = "CRITICAL" THEN 1 ELSE 0 END) >= 1)', [$threshold, $highRepeat, $criticalInstant ? 1 : 0])
            ->get();

        $blocked = 0;
        foreach ($candidates as $c) {
            $created = BlockedIP::firstOrCreate(
                ['ip' => $c->ip],
                ['reason' => "Auto blocked: {$c->total} req/min, risky={$c->risky}"]
            );
            if ($created->wasRecentlyCreated) {
                $blocked++;
            }
        }

        return back()->with('success', "Auto-block hoàn tất. Đã block mới: {$blocked} IP.");
    }

    public function unblock($id)
    {
        BlockedIP::findOrFail($id)->delete();

        return back()->with('success', 'IP đã được bỏ chặn.');
    }
}
