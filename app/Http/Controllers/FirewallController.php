<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $request->validate([
            'ip' => ['required', 'ip'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        BlockedIP::firstOrCreate([
            'ip' => $request->ip,
        ], [
            'reason' => $request->reason,
        ]);

        Cache::forget('blocked_ips_list');

        return back();
    }

    public function unblock($id)
    {
        BlockedIP::findOrFail($id)->delete();

        Cache::forget('blocked_ips_list');

        return back();
    }

}
