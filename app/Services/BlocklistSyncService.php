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
        if (!is_dir($this->dir) && !@mkdir($this->dir, 0775, true) && !is_dir($this->dir)) {
            return ['ok' => false, 'message' => 'Cannot create blocklist dir: ' . $this->dir];
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
        $result = $this->writeFile("{$this->dir}/global-deny.conf", $globalIps);
        if (!$result['ok']) {
            return $result;
        }

        $domainIds = Domain::query()->pluck('id')->all();
        foreach ($domainIds as $id) {
            $result = $this->writeFile("{$this->dir}/domain-{$id}-deny.conf", []);
            if (!$result['ok']) {
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
            $result = $this->writeFile("{$this->dir}/domain-{$domainId}-deny.conf", $ips);
            if (!$result['ok']) {
                return $result;
            }
        }

        $verification = $this->verifyFiles($globalIps, $grouped);
        if (!$verification['ok']) {
            return $verification;
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
        return ['ok' => true, 'message' => 'synced and verified'];
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
        $tmp = '/tmp/' . basename($path);
        $written = @file_put_contents($tmp, $content, LOCK_EX);
        if ($written === false) {
            $error = error_get_last();
            return ['ok' => false, 'message' => 'Cannot write temp file ' . $tmp . ': ' . ($error['message'] ?? 'unknown error')];
        }

        exec('sudo /bin/cp ' . escapeshellarg($tmp) . ' ' . escapeshellarg($path) . ' 2>&1', $copyOut, $copyCode);
        if ($copyCode !== 0) {
            @unlink($tmp);
            return ['ok' => false, 'message' => 'Copy failed for ' . $path . ': ' . implode("\n", $copyOut)];
        }

        exec('sudo /bin/chown root:root ' . escapeshellarg($path) . ' 2>&1', $chownOut, $chownCode);
        exec('sudo /bin/chmod 644 ' . escapeshellarg($path) . ' 2>&1', $chmodOut, $chmodCode);
        @unlink($tmp);

        if ($chownCode !== 0 || $chmodCode !== 0) {
            return ['ok' => false, 'message' => 'Permission finalize failed for ' . $path];
        }

        return ['ok' => true, 'message' => 'written'];
    }

    private function verifyFiles(array $globalIps, $grouped): array
    {
        $globalContent = @file_get_contents("{$this->dir}/global-deny.conf");
        foreach ($globalIps as $ip) {
            if (!str_contains((string) $globalContent, "deny {$ip};")) {
                return ['ok' => false, 'message' => "Verification failed: global deny missing {$ip}"];
            }
        }

        foreach ($grouped as $domainId => $items) {
            $content = @file_get_contents("{$this->dir}/domain-{$domainId}-deny.conf");
            foreach ($items->pluck('ip')->unique()->values()->all() as $ip) {
                if (!str_contains((string) $content, "deny {$ip};")) {
                    return ['ok' => false, 'message' => "Verification failed: domain {$domainId} deny missing {$ip}"];
                }
            }
        }

        return ['ok' => true, 'message' => 'verified'];
    }
}
