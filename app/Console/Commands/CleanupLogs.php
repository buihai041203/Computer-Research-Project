<?php

namespace App\Console\Commands;
use App\Models\TrafficLog;
use Illuminate\Console\Command;

class CleanupLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    TrafficLog::where(
    'created_at',
    '<',
    now()->subDays(30)
    )->delete();

    $this->info('Old logs deleted');

    }
}
