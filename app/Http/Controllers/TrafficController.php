<?php

namespace App\Http\Controllers;

use App\Models\TrafficLog;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TrafficController extends Controller
{
    public function index()
    {
        // Đồng bộ nguồn dữ liệu với trang Logs
        $logs = TrafficLog::latest()->limit(200)->get();

        return view('traffic', compact('logs'));
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
