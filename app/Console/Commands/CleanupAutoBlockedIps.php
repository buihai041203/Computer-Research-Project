<?php

namespace App\Console\Commands;

use App\Models\BlockedIP;
use App\Services\BlocklistSyncService;
use Illuminate\Console\Command;

class CleanupAutoBlockedIps extends Command
{
    protected $signature = 'firewall:cleanup-auto-blocks';

    protected $description = 'Remove expired auto-blocked IP addresses and sync nginx blocklists';

    public function handle(): int
    {
        $minutes = (int) env('AUTO_UNBLOCK_MINUTES', 30);

        // Backward-compatible cleanup for old rows without expires_at
        $legacyDeleted = BlockedIP::query()
            ->where('reason', 'like', 'Auto blocked:%')
            ->whereNull('expires_at')
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->delete();

        // New cleanup based on expires_at
        $expiredDeleted = BlockedIP::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->delete();

        $total = $legacyDeleted + $expiredDeleted;

        $sync = app(BlocklistSyncService::class)->sync();
        if (!$sync['ok']) {
            $this->error('Cleanup done but sync failed: ' . $sync['message']);
            return self::FAILURE;
        }

        $this->info("Auto-unblocked {$total} IP(s). Nginx blocklists synced.");
        return self::SUCCESS;
    }
}
