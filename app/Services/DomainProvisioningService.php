<?php

namespace App\Services;

use App\Models\Domain;
use App\Services\DatabaseProvisioningService;
use App\Jobs\ProvisionSslJob;
use Symfony\Component\Process\Process;

class DomainProvisioningService
{
    public static function provision(Domain $domain): bool
    {
        $sitePath = "/var/www/sites/{$domain->domain}";
        $nginxAvailable = "/etc/nginx/sites-available/{$domain->domain}.conf";
        $nginxEnabled = "/etc/nginx/sites-enabled/{$domain->domain}.conf";

        // Create webroot
        if (!is_dir($sitePath)) {
            if (!mkdir($sitePath, 0755, true) && !is_dir($sitePath)) {
                logger()->error('DomainProvisioningService: failed to create site path', ['path' => $sitePath]);
                return false;
            }
        }

        // Build nginx vhost config
        $config = "server {\n"
            . "    listen 80;\n"
            . "    server_name {$domain->domain};\n"
            . "    root {$sitePath};\n"
            . "    index index.html index.php;\n"
            . "    location / {\n"
            . "        try_files $uri $uri/ /index.php?$query_string;\n"
            . "    }\n"
            . "    location ~ \\.php$ {\n"
            . "        include snippets/fastcgi-php.conf;\n"
            . "        fastcgi_pass unix:/run/php/php8.1-fpm.sock;\n"
            . "    }\n"
            . "    access_log /var/log/nginx/{$domain->domain}.access.log;\n"
            . "    error_log /var/log/nginx/{$domain->domain}.error.log;\n"
            . "}\n";

        if (file_put_contents($nginxAvailable, $config) === false) {
            logger()->error('DomainProvisioningService: failed to write nginx config', ['config' => $nginxAvailable]);
            return false;
        }

        // Enable site
        if (!is_link($nginxEnabled)) {
            if (!symlink($nginxAvailable, $nginxEnabled)) {
                logger()->error('DomainProvisioningService: failed to enable nginx site', ['enabled_path' => $nginxEnabled]);
                return false;
            }
        }

        // Test nginx config
        $process = new Process(['sudo', 'nginx', '-t']);
        $process->setTimeout(15);

        try {
            $process->mustRun();
        } catch (\Exception $e) {
            logger()->error('DomainProvisioningService: nginx test failed', ['message' => $e->getMessage()]);
            return false;
        }

        // Reload nginx
        $process = new Process(['sudo', 'systemctl', 'reload', 'nginx']);
        $process->setTimeout(15);

        try {
            $process->mustRun();
        } catch (\Exception $e) {
            logger()->error('DomainProvisioningService: nginx reload failed', ['message' => $e->getMessage()]);
            return false;
        }

        $domain->update(['status' => 'provisioned']);

        $dbInstance = DatabaseProvisioningService::provision($domain);

        if (! $dbInstance) {
            $domain->update(['status' => 'nginx_provisioned_db_failed']);
            return false;
        }

        ProvisionSslJob::dispatch($domain);

        return true;
    }
}
