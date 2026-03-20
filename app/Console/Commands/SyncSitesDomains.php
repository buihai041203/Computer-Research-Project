<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;
use Illuminate\Support\Str;

class SyncSitesDomains extends Command
{
protected $signature = 'sites:sync';
protected $description = 'Sync domains from /var/www/sites';

public function handle()
{
$base = '/var/www/sites';

if (!is_dir($base)) {
$this->error("Directory not found: $base");
return self::FAILURE;
}

$dirs = array_filter(scandir($base), function ($d) use ($base) {
return $d !== '.' && $d !== '..' && is_dir($base . '/' . $d);
});

foreach ($dirs as $site) {
$existing = Domain::where('domain', $site)->first();

Domain::updateOrCreate(
['domain' => $site],
[
'root_path' => $base . '/' . $site,
'php_version' => '8.4',
'status' => 'active',
'is_active' => true,
'agent_key' => $existing?->agent_key ?? Str::random(32),
]
);
}

$this->info('Sync completed: ' . count($dirs) . ' sites');
return self::SUCCESS;
}
}
