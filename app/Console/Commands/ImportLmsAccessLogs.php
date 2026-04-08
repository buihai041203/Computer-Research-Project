<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\TrafficLog;
use App\Services\ThreatAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportLmsAccessLogs extends Command
{
    protected $signature = 'traffic:import-lms-access';
    protected $description = 'Import LMS traffic from nginx access logs into traffic_logs';

    private string $statePath = '/var/www/panel/storage/app/lms-import-state.json';

    public function handle(): int
    {
        $domain = Domain::query()->where('domain', 'lms')->first();
        if (!$domain) {
            $this->error('Domain lms not found.');
            return self::FAILURE;
        }

        $files = [
            '/var/log/nginx/access.log',
            '/var/log/nginx/access.log.1',
        ];

        $state = $this->loadState();
        $imported = 0;

        foreach ($files as $file) {
            if (!is_readable($file)) {
                continue;
            }

            $real = realpath($file) ?: $file;
            $mtime = @filemtime($file) ?: time();
            $lastMtime = (int) ($state[$real]['mtime'] ?? 0);

            if ($lastMtime > 0 && $mtime <= $lastMtime) {
                continue;
            }

            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $lines = array_slice($lines, -500);

            foreach ($lines as $line) {
                if (!str_contains($line, '/lms/')) {
                    continue;
                }

                if (!preg_match('/^(?<ip>\S+) \S+ \S+ \[(?<time>[^"]*[^\]])\] "(?<method>[A-Z]+) (?<path>[^ ]+) [^"]+" (?<status>\d{3}) \S+ "(?<referer>[^"]*)" "(?<ua>[^"]*)"/', $line, $m)) {
                    continue;
                }

                $method = strtoupper($m['method'] ?? 'GET');
                $path = $m['path'] ?? '';
                if (!str_starts_with($path, '/lms/')) {
                    continue;
                }

                if (preg_match('/\.(css|js|map|png|jpg|jpeg|gif|svg|ico|webp|woff|woff2|ttf|eot|txt|xml)$/i', $path)) {
                    continue;
                }

                $isLoginSubmit = $method === 'POST' && $path === '/lms/register/login.php';
                $isLoginSuccessLanding = $method === 'GET' && $path === '/lms/main/main.php';

                if (!$isLoginSubmit && !$isLoginSuccessLanding) {
                    continue;
                }

                $createdAt = Carbon::createFromFormat('d/M/Y:H:i:s O', $m['time'])->utc();
                $fingerprint = sha1(($m['ip'] ?? '') . '|' . $method . '|' . $path . '|' . ($m['status'] ?? '') . '|' . ($m['ua'] ?? '') . '|' . $createdAt->timestamp);

                if (($state[$real]['fingerprints'][$fingerprint] ?? false) === true) {
                    continue;
                }

                $ip = $m['ip'];
                $status = (int) $m['status'];
                $ua = $m['ua'] ?: 'unknown';
                $type = str_contains(strtolower($ua), 'bot') ? 'bot' : 'human';
                $isBot = $type === 'bot';
                $threat = ThreatAnalyzer::analyze($ip, $type, 'Unknown');

                TrafficLog::create([
                    'domain_id' => $domain->id,
                    'domain' => 'lms',
                    'request_path' => $path,
                    'status_code' => $status,
                    'ip' => $ip,
                    'country' => 'Unknown',
                    'is_bot' => $isBot,
                    'user_agent' => $ua,
                    'type' => $type,
                    'threat' => $threat['level'] ?? 'LOW',
                    'browser' => substr($ua, 0, 255),
                    'device' => 'nginx-import',
                    'source' => 'import',
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ]);

                $state[$real]['fingerprints'][$fingerprint] = true;
                $imported++;
            }

            $state[$real]['mtime'] = $mtime;
            $state[$real]['fingerprints'] = array_slice($state[$real]['fingerprints'] ?? [], -1000, null, true);
        }

        $this->saveState($state);
        $this->info("Imported {$imported} LMS access log entries.");
        return self::SUCCESS;
    }

    private function loadState(): array
    {
        if (!is_file($this->statePath)) {
            return [];
        }

        $data = json_decode((string) @file_get_contents($this->statePath), true);
        return is_array($data) ? $data : [];
    }

    private function saveState(array $state): void
    {
        $dir = dirname($this->statePath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents($this->statePath, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
