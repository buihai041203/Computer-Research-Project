<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

    public function toggle($id) 
    {
        $domain = Domain::findOrFail($id);
        $domain->is_active = !($domain->is_active ?? true);
        $domain->save();

        $this->syncNginxState($domain->domain, $domain->is_active);
        shell_exec('sudo systemctl reload nginx');

        return back()->with('success', 'Status updated!');
    }

    public function bulkToggle(Request $request)
    {
        $request->validate([
            'domain_ids' => 'required|array',
            'action' => 'required|in:on,off'
        ]);

        $newState = $request->action === 'on';
        $domains = Domain::whereIn('id', $request->domain_ids)->get();

        foreach ($domains as $domain) {
            $domain->is_active = $newState;
            $domain->save();
            $this->syncNginxState($domain->domain, $newState);
        }

        shell_exec('sudo systemctl reload nginx');
        return back()->with('success', 'Đã cập nhật trạng thái hàng loạt!');
    }

    // --- CÁC HÀM AUTO-OFF & NGINX ---

    public static function emergencyShutdown($domainName)
    {
        $domain = Domain::where('domain', $domainName)->first();
        if ($domain && $domain->is_active) {
            $domain->is_active = false;
            $domain->save();
            
            $enabledPath = "/etc/nginx/sites-enabled/{$domainName}";
            if (file_exists($enabledPath)) {
                shell_exec("sudo rm {$enabledPath}");
                shell_exec('sudo systemctl reload nginx');
            }
            Log::alert("AUTO OFF: Đã giật sập Nginx của {$domainName} do phát hiện tấn công!");
        }
    }

    private function syncNginxState($domainName, $isActive)
    {
        $availablePath = "/etc/nginx/sites-available/{$domainName}";
        $enabledPath = "/etc/nginx/sites-enabled/{$domainName}";

        if ($isActive) {
            if (file_exists($availablePath) && !file_exists($enabledPath)) {
                shell_exec("sudo ln -s {$availablePath} {$enabledPath}");
            }
        } else {
            if (file_exists($enabledPath)) {
                shell_exec("sudo rm {$enabledPath}");
            }
        }
    }
}