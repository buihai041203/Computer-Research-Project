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
        $human = TrafficLog::where('type', 'human')
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $bot = TrafficLog::where('type', 'bot')
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        return response()->json([
            'human' => $human,
            'bot' => $bot,
        ]);
    }
}
