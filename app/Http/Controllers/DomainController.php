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

    public function store(Request $request)
    {
        $request->validate(['domain' => 'required|unique:domains,domain']);

        $phpVersion = $request->php_version ?? '8.4';
        $rootPath = '/var/www/sites/' . $request->domain;

        Domain::create([
            'domain' => $request->domain,
            'agent_key' => Str::random(32),
            'root_path' => $rootPath,
            'php_version' => $phpVersion,
            'status' => 'pending_setup',
            'is_active' => true 
        ]);

        return back()->with('success', 'Domain added successfully!');
    }

    public function destroy($id)
    {
        $domain = Domain::findOrFail($id);
        $domainName = $domain->domain;
        $domain->delete();

        // Xóa cấu hình Nginx
        $enabledPath = "/etc/nginx/sites-enabled/{$domainName}";
        if (file_exists($enabledPath)) {
            shell_exec("sudo rm {$enabledPath}");
            shell_exec('sudo systemctl reload nginx');
        }

        return back()->with('success', 'Domain deleted successfully!');
    }
}
