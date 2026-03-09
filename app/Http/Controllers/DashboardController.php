<?php

namespace App\Http\Controllers;

use App\Models\Visitor;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVisitors = Visitor::count();

        $humanVisitors = Visitor::where('is_bot', false)->count();

        $botVisitors = Visitor::where('is_bot', true)->count();

        $latestVisitors = Visitor::latest()->limit(10)->get();

        return view('dashboard', compact(
            'totalVisitors',
            'humanVisitors',
            'botVisitors',
            'latestVisitors'
        ));
    }
}
