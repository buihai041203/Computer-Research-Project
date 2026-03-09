<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;

class DomainController extends Controller
{

    public function index()
    {

        $domains = Domain::latest()->get();

        return view('domains.index',compact('domains'));

    }

    public function store(Request $request)
    {

        $value = $request->domain;

        $type = filter_var($value, FILTER_VALIDATE_IP)
            ? 'ip'
            : 'domain';

        Domain::create([
            'domain'=>$value,
            'type'=>$type
        ]);

        return back();

    }

}
