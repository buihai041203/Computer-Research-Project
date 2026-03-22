<?php

namespace App\Http\Controllers;

use App\Models\TrafficLog;

class DashboardController extends Controller
{
    public function index()
    {
        // Đồng bộ với trang Traffic/Logs: cùng nguồn traffic_logs, cùng sample window
        $sample = TrafficLog::latest()->limit(200)->get(['type']);

        $totalVisitors = $sample->count();
        $humanVisitors = $sample->where('type', 'human')->count();
        $botVisitors = $sample->where('type', 'bot')->count();

        $latestVisitors = TrafficLog::latest()->limit(10)->get();

        return view('dashboard', compact(
            'totalVisitors',
            'humanVisitors',
            'botVisitors',
            'latestVisitors'
        ));
    }
}
