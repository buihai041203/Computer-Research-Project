<?php

namespace App\Http\Controllers;

use App\Models\SiteDatabase;
use App\Models\DatabaseAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseManagerController extends Controller
{
public function index()
{
$sites = SiteDatabase::orderBy('site_name')->get();
return view('databases.index', compact('sites'));
}

public function updateConfig(Request $request, $site)
{
$data = $request->validate([
'db_host' => 'required|string|max:255',
'db_port' => 'required|integer|min:1|max:65535',
'db_name' => 'required|string|max:255',
'db_user' => 'required|string|max:255',
'db_password' => 'nullable|string|max:255',
]);

$row = SiteDatabase::where('site_name', $site)->firstOrFail();
$row->update($data);

return back()->with('success', "Updated DB config for {$site}");
}

public function show($site)
{
$cfg = SiteDatabase::where('site_name', $site)->firstOrFail();
$conn = $this->connectSite($cfg);

$tablesRaw = DB::connection($conn)->select('SHOW TABLES');
$tables = array_map(fn($r) => array_values((array)$r)[0], $tablesRaw);

return view('databases.show', compact('cfg', 'tables'));
}

public function table($site, $table)
{
$cfg = SiteDatabase::where('site_name', $site)->firstOrFail();
$conn = $this->connectSite($cfg);

$safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
abort_if($safeTable !== $table, 400, 'Invalid table name');

$columns = DB::connection($conn)->select("DESCRIBE `$safeTable`");
$rows = DB::connection($conn)->table($safeTable)->limit(100)->get();

return view('databases.table', compact('cfg', 'table', 'columns', 'rows'));
}

public function runQuery(Request $request, $site)
{
$request->validate([
'sql' => 'required|string|max:20000',
]);

$sql = trim($request->sql);

// chặn query nguy hiểm
if (preg_match('/\b(drop|truncate|alter|grant|revoke|create\s+user)\b/i', $sql)) {
return back()->with('error', 'Blocked dangerous SQL command.');
}

$cfg = SiteDatabase::where('site_name', $site)->firstOrFail();
$conn = $this->connectSite($cfg);

try {
$result = DB::connection($conn)->select($sql);

DatabaseAudit::create([
'user_id' => auth()->id(),
'site_name' => $site,
'action' => 'query',
'query_text' => $sql,
'ip' => $request->ip(),
]);

return back()->with('success', 'Query executed successfully.')
->with('query_result', $result);
} catch (\Throwable $e) {
return back()->with('error', $e->getMessage());
}
}

private function connectSite(SiteDatabase $cfg): string
{
$name = 'site_runtime_'.$cfg->id;

config([
"database.connections.$name" => [
'driver' => $cfg->db_connection ?: 'mysql',
'host' => $cfg->db_host,
'port' => $cfg->db_port,
'database' => $cfg->db_name,
'username' => $cfg->db_user,
'password' => $cfg->db_password,
'charset' => 'utf8mb4',
'collation' => 'utf8mb4_unicode_ci',
'prefix' => '',
'strict' => true,
'engine' => null,
]
]);

DB::purge($name);
DB::reconnect($name);

return $name;
}
}
