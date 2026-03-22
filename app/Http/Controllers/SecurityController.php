<?php

namespace App\Http\Controllers;

use App\Models\SecurityEvent;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index()
    {
        $events = SecurityEvent::latest()->get();

        return view('security.index', compact('events'));
    }

    public function clear(Request $request)
    {
        SecurityEvent::truncate();

        return back()->with('success', 'Security logs đã được xóa.');
    }
}
