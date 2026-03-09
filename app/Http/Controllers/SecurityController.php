<?php

namespace App\Http\Controllers;

use App\Models\SecurityEvent;

class SecurityController extends Controller
{

    public function index()
    {

        $events = SecurityEvent::latest()->get();

        return view('security.index',compact('events'));

    }

}
