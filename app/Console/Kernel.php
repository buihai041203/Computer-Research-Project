<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        // Dọn log cũ
        $schedule->command('app:cleanup-logs')->dailyAt('02:00');

        // Backup DB mỗi ngày
        $schedule->command('backup:database')->dailyAt('02:30');

        // Gỡ block tự động đã hết hạn
        $schedule->command('firewall:cleanup-auto-blocks')->everyTenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}
