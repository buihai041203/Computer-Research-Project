<?php

namespace App\Services;

use App\Models\BlockedIP;
use App\Models\Domain;

class BlocklistSyncService
{
    private string $dir = '/etc/nginx/blocklists';

    public function sync(): array
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }

        $rows = BlockedIP::query()
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        // 1) Global
        $globalIps = $rows->where('scope_type', 'global')->pluck('ip')->unique()->values()->all();
        $this->writeFile("{$this->dir}/global-deny.conf", $globalIps);

        // 2) Init file cho mọi domain
        $domainIds = Domain::query()->pluck('id')->all();
        foreach ($domainIds as $id) {
            $this->writeFile("{$this->dir}/domain-{$id}-deny.conf", []);
        }

        // 3) Fill domain-scope
        $grouped = $rows->where('scope_type', 'domain')->groupBy('scope_value');
        foreach ($grouped as $domainId => $items) {
            $ips = $items->pluck('ip')->unique()->values()->all();
            $this->writeFile("{$this->dir}/domain-{$domainId}-deny.conf", $ips);
        }

        exec('sudo /usr/sbin/nginx -t 2>&1', $testOut, $testCode);
        if ($testCode !== 0) {
            return ['ok' => false, 'message' => 'nginx -t failed: ' . implode("\n", $testOut)];
        }

        exec('sudo /bin/systemctl reload nginx 2>&1', $reloadOut, $reloadCode);
        if ($reloadCode !== 0) {
            return ['ok' => false, 'message' => 'reload failed: ' . implode("\n", $reloadOut)];
        }

        return ['ok' => true, 'message' => 'synced'];
    }

    private function writeFile(string $path, array $ips): void
    {
        $content = "# auto-generated\n";
        foreach ($ips as $ip) {
            $content .= "deny {$ip};\n";
        }
        file_put_contents($path, $content, LOCK_EX);
    }
}
