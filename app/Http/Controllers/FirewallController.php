<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockedIP;

class FirewallController extends Controller
{

    public function index()
    {
        $ips = BlockedIP::latest()->get();

        return view('firewall.index',compact('ips'));
    }

    public function block(Request $request)
    {
        BlockedIP::create([
            'ip'=>$request->ip,
            'reason'=>$request->reason
        ]);

        return back();
    }

    public function unblock($id)
    {
        BlockedIP::findOrFail($id)->delete();

        return back();
    }

}
