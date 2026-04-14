<?php

namespace App\Http\Controllers;

use App\Models\TrafficLog;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class TrafficController extends Controller
{
    public function index()
    {
        // Đồng bộ nguồn dữ liệu với trang Logs
        $logs = TrafficLog::latest()->limit(200)->get();

        $logs->transform(function ($log) {
            if (($log->country ?? 'Unknown') !== 'Unknown') {
                return $log;
            }

            $resolvedCountry = $this->resolveCountryForDisplay((string) $log->ip);
            if ($resolvedCountry !== 'Unknown') {
                $log->country = $resolvedCountry;
            }

            return $log;
        });

        return view('traffic', compact('logs'));
    }

    private function resolveCountryForDisplay(string $ip): string
    {
        try {
            $geo = Http::timeout(2)->get('http://ip-api.com/json/' . $ip)->json();
            if (($geo['status'] ?? null) === 'success' && !empty($geo['country'])) {
                return (string) $geo['country'];
            }
        } catch (\Throwable $e) {
            // fall through
        }

        try {
            $geo = Http::timeout(2)->get('https://ipinfo.io/' . $ip . '/json')->json();
            if (!empty($geo['country'])) {
                return match (strtoupper((string) $geo['country'])) {
                    'VN' => 'Vietnam',
                    'US' => 'United States',
                    'CN' => 'China',
                    'SG' => 'Singapore',
                    'RU' => 'Russia',
                    'DE' => 'Germany',
                    'FR' => 'France',
                    'JP' => 'Japan',
                    'KR' => 'South Korea',
                    'IN' => 'India',
                    'BR' => 'Brazil',
                    'GB' => 'United Kingdom',
                    'NL' => 'Netherlands',
                    'CA' => 'Canada',
                    'AU' => 'Australia',
                    default => 'Unknown',
                };
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return 'Unknown';
    }

    public function clear(Request $request)
    {
        TrafficLog::truncate();

        // Bảng visitors để tương thích cũ (nếu còn dùng ở nơi khác)
        if (Schema::hasTable('visitors')) {
            Visitor::truncate();
        }

        return back()->with('success', 'Traffic logs đã được xóa.');
    }

    public function stats()
    {
        // Đồng bộ với trang Traffic: cùng nguồn traffic_logs và cùng cửa sổ mẫu gần nhất
        $sample = TrafficLog::latest()->limit(200)->get(['type']);

        $human = $sample->where('type', 'human')->count();
        $bot = $sample->where('type', 'bot')->count();

        return response()->json([
            'human' => $human,
            'bot' => $bot,
            'total' => $sample->count(),
        ]);
    }
}
