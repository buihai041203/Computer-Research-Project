<?php

namespace App\Console\Commands;

use App\Models\BlockedIP;
use Illuminate\Console\Command;

class CleanupAutoBlockedIps extends Command
{
    protected $signature = 'firewall:cleanup-auto-blocks';

    protected $description = 'Remove expired auto-blocked IP addresses';

    public function handle(): int
    {
        $minutes = (int) env('AUTO_UNBLOCK_MINUTES', 30);

        $deleted = BlockedIP::query()
            ->where('reason', 'like', 'Auto blocked:%')
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->delete();

        $this->info("Auto-unblocked {$deleted} IP(s).");
        return self::SUCCESS;
    }
}
