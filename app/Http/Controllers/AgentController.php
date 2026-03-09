<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrafficLog;
use App\Models\Domain;

class AgentController extends Controller
{

    public function collect(Request $request)
{

    $domain = $request->domain;
    $key = $request->key;

    $exists = Domain::where([
        'domain'=>$domain,
        'agent_key'=>$key
    ])->first();

    if(!$exists){
        return response()->json([
            'status'=>'invalid'
        ]);
    }

    // tiếp tục log traffic

}

}
