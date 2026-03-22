<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Backup MySQL database to local storage/backups';

    public function handle(): int
    {
        $connection = config('database.default');
        $cfg = config("database.connections.{$connection}");

        if (($cfg['driver'] ?? null) !== 'mysql') {
            $this->error('Only mysql driver is supported by this backup command.');
            return self::FAILURE;
        }

        $host = $cfg['host'] ?? '127.0.0.1';
        $port = (string) ($cfg['port'] ?? 3306);
        $db = $cfg['database'] ?? null;
        $user = $cfg['username'] ?? null;
        $pass = $cfg['password'] ?? '';

        if (!$db || !$user) {
            $this->error('DB config is incomplete.');
            return self::FAILURE;
        }

        $dir = env('BACKUP_PATH', storage_path('backups'));
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            $this->error("Cannot create backup directory: {$dir}");
            return self::FAILURE;
        }

        $file = rtrim($dir, '/') . '/db_' . $db . '_' . now()->format('Ymd_His') . '.sql.gz';

        $cmd = sprintf(
            'MYSQL_PWD=%s mysqldump -h %s -P %s -u %s --single-transaction --quick --routines --triggers %s | gzip > %s',
            escapeshellarg($pass),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($db),
            escapeshellarg($file)
        );

        exec($cmd . ' 2>&1', $out, $code);

        if ($code !== 0) {
            $this->error('Backup failed: ' . implode("\n", $out));
            return self::FAILURE;
        }

        $retention = (int) env('BACKUP_RETENTION_DAYS', 7);
        foreach (glob(rtrim($dir, '/') . '/db_' . $db . '_*.sql.gz') ?: [] as $f) {
            if (filemtime($f) < now()->subDays($retention)->timestamp) {
                @unlink($f);
            }
        }

        $this->info('Backup created: ' . $file);
        return self::SUCCESS;
    }
}
