<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncDomainNginx extends Command
{
    protected $signature = 'domains:sync-nginx {--domain=}';
    protected $description = 'Sync Nginx configs for domains';

    public function handle()
    {
        $domains = $this->option('domain')
            ? Domain::where('domain', $this->option('domain'))->get()
            : Domain::all();

        foreach ($domains as $domain) {
            $configPath = "/etc/nginx/sites-available/{$domain->domain}";
            $enabledPath = "/etc/nginx/sites-enabled/{$domain->domain}";

            $config = view('nginx.domain', compact('domain'))->render();

            // Write with sudo (use sudo tee or custom helper)
            $tmpFile = "/tmp/nginx-{$domain->domain}.conf";
            file_put_contents($tmpFile, $config);
            exec("sudo cp {$tmpFile} {$configPath} && sudo chown www-data:www-data {$configPath}");
            unlink($tmpFile);

            // Symlink management
            if ($domain->is_active) {
                exec("sudo ln -sf {$configPath} {$enabledPath}");
                $this->info("✅ Enabled: {$domain->domain}");
            } else {
                exec("sudo rm -f {$enabledPath}");
                $this->info("❌ Disabled: {$domain->domain}");
            }
        }

        exec('sudo nginx -t && sudo systemctl reload nginx');
        $this->info('🎉 Nginx reloaded successfully!');
    }
}
