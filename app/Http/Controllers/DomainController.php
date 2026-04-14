<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Artisan;
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
        $domainName = trim((string) $request->domain);
        $rootPath = '/var/www/sites/' . $domainName;
        $agentKey = $this->resolveStableAgentKey($domainName) ?? Str::random(32);

        Domain::create([
            'domain' => $domainName,
            'agent_key' => $agentKey,
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

    private function resolveStableAgentKey(string $domainName): ?string
    {
        $candidateFiles = [
            "/var/www/sites/{$domainName}/register/db_connect.php",
        ];

        foreach ($candidateFiles as $file) {
            if (!is_readable($file)) {
                continue;
            }

            $content = @file_get_contents($file);
            if ($content === false) {
                continue;
            }

            if (preg_match("/['\"]key['\"]\s*=>\s*['\"]([^'\"]+)['\"]/, $content, $m)) {
                return trim($m[1]);
            }
        }

        return null;
    }

}
