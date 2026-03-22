<?php

namespace App\Http\Controllers;

use App\Models\TrafficLog;
use App\Models\Visitor;
use Illuminate\Http\Request;

class TrafficController extends Controller
{
    public function index()
    {
        $visitors = Visitor::latest()->limit(50)->get();

        return view('traffic', compact('visitors'));
    }

    public function clear(Request $request)
    {
        Visitor::truncate();
        TrafficLog::truncate();

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
