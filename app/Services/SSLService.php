<?php

namespace App\Services;

use App\Models\Domain;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class SSLService
{
    public static function requestCertificate(Domain $domain): bool
    {
        $webroot = "/var/www/sites/{$domain->domain}";
        $email = env('SSL_ADMIN_EMAIL', 'admin@' . $domain->domain);

        $cmd = [
            'sudo', 'certbot', 'certonly', '--webroot',
            '-w', $webroot,
            '-d', $domain->domain,
            '--non-interactive',
            '--agree-tos',
            '-m', $email,
            '--quiet',
        ];

        $process = new Process($cmd);
        $process->setTimeout(180);

        try {
            $process->mustRun();
            return true;
        } catch (\Exception $e) {
            $output = $process->getOutput() . ' ' . $process->getErrorOutput();
            if (stripos($output, 'rate limit') !== false) {
                $domain->update(['status' => 'ssl_rate_limited']);
            } else {
                $domain->update(['status' => 'ssl_failed']);
            }

            Log::error('SSLService::requestCertificate failed', [
                'domain' => $domain->domain,
                'error' => $e->getMessage(),
                'output' => $output,
            ]);

            return false;
        }
    }

    public static function enableHttpsConfig(Domain $domain): bool
    {
        $nginxAvailable = "/etc/nginx/sites-available/{$domain->domain}.conf";
        $sitePath = "/var/www/sites/{$domain->domain}";

        $certPath = "/etc/letsencrypt/live/{$domain->domain}/fullchain.pem";
        $keyPath = "/etc/letsencrypt/live/{$domain->domain}/privkey.pem";

        if (!file_exists($certPath) || !file_exists($keyPath)) {
            Log::error('SSLService::enableHttpsConfig missing cert files', ['domain' => $domain->domain]);
            return false;
        }

        $config = "server {\n"
            . "    listen 80;\n"
            . "    server_name {$domain->domain};\n"
            . "    root {$sitePath};\n"
            . "    return 301 https://$host$request_uri;\n"
            . "}\n\n"
            . "server {\n"
            . "    listen 443 ssl;\n"
            . "    server_name {$domain->domain};\n"
            . "    root {$sitePath};\n"
            . "    index index.html index.php;\n"
            . "    ssl_certificate {$certPath};\n"
            . "    ssl_certificate_key {$keyPath};\n"
            . "    ssl_protocols TLSv1.2 TLSv1.3;\n"
            . "    ssl_ciphers 'EECDH+AESGCM:EDH+AESGCM';\n"
            . "    ssl_prefer_server_ciphers on;\n"
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
            Log::error('SSLService::enableHttpsConfig failed write', ['domain' => $domain->domain]);
            return false;
        }

        $reload = new Process(['sudo', 'systemctl', 'reload', 'nginx']);
        $reload->setTimeout(15);

        try {
            $reload->mustRun();
            return true;
        } catch (\Exception $e) {
            Log::error('SSLService::enableHttpsConfig nginx reload failed', ['domain' => $domain->domain, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public static function renewCertificates(): bool
    {
        $process = new Process(['sudo', 'certbot', 'renew', '--quiet']);
        $process->setTimeout(300);

        try {
            $process->mustRun();
            $reload = new Process(['sudo', 'systemctl', 'reload', 'nginx']);
            $reload->setTimeout(15);
            $reload->mustRun();
            return true;
        } catch (\Exception $e) {
            Log::error('SSLService::renewCertificates failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
