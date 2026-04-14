<?php

namespace App\Services;

use App\Models\BlockedIp;
use App\Models\Domain;

class BlocklistSyncService
{
    private string $stagingDir = '/tmp/panel-blocklists';
    private string $wrapper = '/usr/local/bin/panel-sync-blocklists';

    public function sync(): array
    {
        if (!is_dir($this->stagingDir) && !@mkdir($this->stagingDir, 0775, true) && !is_dir($this->stagingDir)) {
            return ['ok' => false, 'message' => 'Cannot create staging dir: ' . $this->stagingDir];
        }

        $rows = BlockedIp::query()
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        $globalIps = $rows->where('scope_type', 'global')->pluck('ip')->unique()->values()->all();
        $result = $this->writeFile("{$this->stagingDir}/global-deny.conf", $globalIps);
        if (!($result['ok'] ?? false)) {
            return $result;
        }

        $domains = Domain::query()->get(['id', 'domain']);
        $domainFileMap = $domains->mapWithKeys(function ($domain) {
            return [$domain->id => $this->domainFileName($domain->domain)];
        });

        foreach ($domainFileMap as $fileName) {
            $result = $this->writeFile("{$this->stagingDir}/{$fileName}", []);
            if (!($result['ok'] ?? false)) {
                return $result;
            }
        }

        $grouped = $rows->where('scope_type', 'domain')->groupBy('scope_value');
        foreach ($grouped as $domainId => $items) {
            $fileName = $domainFileMap->get((int) $domainId);
            if (!$fileName) {
                continue;
            }

            $ips = $items->pluck('ip')->unique()->values()->all();
            $result = $this->writeFile("{$this->stagingDir}/{$fileName}", $ips);
            if (!($result['ok'] ?? false)) {
                return $result;
            }
        }

        exec('sudo ' . escapeshellarg($this->wrapper) . ' 2>&1', $out, $code);
        if ($code !== 0) {
            return ['ok' => false, 'message' => 'wrapper sync failed: ' . implode("\n", $out)];
        }

        return ['ok' => true, 'message' => 'staged, synced and reloaded'];
    }

    private function domainFileName(string $domain): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9.-]+/', '-', strtolower($domain));
        $safe = trim((string) $safe, '-.');
        return 'domain-' . ($safe !== '' ? $safe : 'unknown') . '-deny.conf';
    }

    private function writeFile(string $path, array $ips): array
    {
        $content = "# auto-generated\n";
        foreach ($ips as $ip) {
            $content .= "deny {$ip};\n";
        }

        $written = @file_put_contents($path, $content, LOCK_EX);
        if ($written === false) {
            $error = error_get_last();
            return ['ok' => false, 'message' => 'Cannot write staging file ' . $path . ': ' . ($error['message'] ?? 'unknown error')];
        }

        @chmod($path, 0664);

        return ['ok' => true, 'message' => 'written'];
    }
}
