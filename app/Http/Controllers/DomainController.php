<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain;
use App\Services\DomainProvisioningService;
use Illuminate\Support\Str;

class DomainController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $domains = Domain::latest()->get();
        } else {
            $domains = Domain::where('user_id', Auth::id())->latest()->get();
        }

        return view('domains.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Domain::class);

        $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain', 'regex:/^(?!-)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9-_]+$/']
        ]);

        if (! Auth::user()->canCreateDomain()) {
            return back()->withErrors(['domain' => 'Domain limit reached for your plan']);
        }

        $domain = Domain::create([
            'domain' => $request->domain,
            'type' => filter_var($request->domain, FILTER_VALIDATE_IP) ? 'ip' : 'domain',
            'agent_key' => Str::random(32),
            'status' => 'pending',
            'user_id' => Auth::id(),
        ]);

        DomainProvisioningService::provision($domain);

        return back();
    }

    public function show(Domain $domain)
    {
        $this->authorize('view', $domain);

        return view('domains.show', [
            'domain' => $domain,
            'database' => $domain->databaseInstance,
        ]);
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('delete', $domain);

        // TODO: remove nginx/site/db resources (safe cleanup)
        $domain->delete();

        return back();
    }

}
