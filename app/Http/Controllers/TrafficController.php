<?php

namespace App\Http\Controllers;

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
        $human = rand(10,50);
        $bot = rand(1,20);

        return response()->json([
            'human' => $human,
            'bot' => $bot
        ]);
    }

}
