<?php

namespace App\Http\Controllers;

use App\Models\TrafficLog;

class LogController extends Controller
{
    public function index()
    {
        $logs = TrafficLog::latest()->limit(200)->get();

        return view('logs.index', compact('logs'));
    }
}
