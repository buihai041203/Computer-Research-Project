<?php

namespace App\Http\Controllers;
use App\Models\TrafficLog;
use Illuminate\Http\Request;
use App\Models\Visitor;

class TrafficController extends Controller
{

    public function index()
    {
        $visitors = Visitor::latest()->limit(50)->get();

        return view('traffic', compact('visitors'));
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
    'bot' => $bot
    ]);
    }

}
