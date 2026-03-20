<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SiteDatabase;

class SyncSiteDatabases extends Command
{
protected $signature = 'sites:sync-db';
protected $description = 'Sync /var/www/sites folders into site_databases';

public function handle()
{
$base = '/var/www/sites';

if (!is_dir($base)) {
$this->error("Not found: $base");
return self::FAILURE;
}

$sites = array_filter(scandir($base), function ($d) use ($base) {
return $d !== '.' && $d !== '..' && is_dir("$base/$d");
});

foreach ($sites as $site) {
SiteDatabase::firstOrCreate(
['site_name' => $site],
[
'db_connection' => 'mysql',
'db_host' => '127.0.0.1',
'db_port' => 3306,
'is_active' => true,
]
);
}

$this->info('Synced: '.count($sites).' sites');
return self::SUCCESS;
}
}
