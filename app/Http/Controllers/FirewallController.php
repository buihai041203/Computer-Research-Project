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

        return view('firewall.index', compact('ips', 'suspicious'));
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

        $candidates = TrafficLog::query()
            ->selectRaw('ip, COUNT(*) as total, SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) as risky')
            ->where('created_at', '>=', now()->subMinute())
            ->groupBy('ip')
            ->havingRaw('COUNT(*) > ? OR SUM(CASE WHEN threat IN ("HIGH","CRITICAL") THEN 1 ELSE 0 END) >= 3', [$threshold])
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
