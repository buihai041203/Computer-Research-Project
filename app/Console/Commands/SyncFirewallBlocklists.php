<?php

namespace App\Console\Commands;

use App\Services\BlocklistSyncService;
use Illuminate\Console\Command;

class SyncFirewallBlocklists extends Command
{
    protected $signature = 'firewall:sync-blocklists';
    protected $description = 'Rebuild nginx firewall blocklists from blocked_ips and verify the applied state';

    public function handle(): int
    {
        $result = app(BlocklistSyncService::class)->sync();

        if (!($result['ok'] ?? false)) {
            $this->error($result['message'] ?? 'Firewall blocklist sync failed');
            return self::FAILURE;
        }

        $this->info($result['message'] ?? 'Firewall blocklists synced');
        return self::SUCCESS;
    }
}
