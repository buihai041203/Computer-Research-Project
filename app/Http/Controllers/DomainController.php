<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::latest()->get();

        return view('domains.index', compact('domains'));
    }

    public function create()
    {
        return view('domains.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain'],
            'ip' => ['nullable', 'ip'],
        ]);

        $type = filter_var($data['domain'], FILTER_VALIDATE_IP) ? 'ip' : 'domain';

        Domain::create([
            'domain' => $data['domain'],
            'ip' => $data['ip'] ?? null,
            'type' => $type,
            'agent_key' => Str::random(32),
        ]);

        return redirect()->route('domains.index')->with('success', 'Domain added successfully.');
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domain deleted.');
    }
}

