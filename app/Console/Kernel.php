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

        // Import traffic LMS từ Nginx access log
        $schedule->command('traffic:import-lms-access')->everyMinute();

        // Gỡ block tự động đã hết hạn
        $schedule->command('firewall:cleanup-auto-blocks')->everyTenMinutes();

        // Đồng bộ lại blocklists từ DB -> nginx
        $schedule->command('firewall:sync-blocklists')->everyMinute();

        // Enrich country nền, không chặn luồng import chính
        $schedule->command('traffic:enrich-country --limit=50')->everyFiveMinutes();

        $schedule->command('domains:sync-nginx')->everyTenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}
