<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\SiteDatabase;
use App\Models\DatabaseAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseManagerController extends Controller
{
    public function index()
    {
        $domains = Domain::latest()->with('databaseConfig')->get();
        return view('databases.index', compact('domains'));
    }

    public function updateConfig(Request $request, $domain)
    {
        $data = $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_name' => 'required|string|max:255',
            'db_user' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $domainModel = Domain::where('domain', $domain)->firstOrFail();

        $cfg = SiteDatabase::firstOrNew(['domain_id' => $domainModel->id]);
        $cfg->site_name = $domainModel->domain;
        $cfg->db_connection = 'mysql';
        $cfg->db_host = $data['db_host'];
        $cfg->db_port = $data['db_port'];
        $cfg->db_name = $data['db_name'];
        $cfg->db_user = $data['db_user'];

        if (!empty($data['db_password'])) {
            $cfg->db_password = $data['db_password']; // mutator tự encrypt
        }

        $cfg->is_active = (bool)($data['is_active'] ?? true);
        $cfg->save();

        return back()->with('success', "Updated DB config for {$domain}");
    }

    public function show($domain)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->first();

        $tables = [];
        if ($cfg && $cfg->is_active && $cfg->db_name && $cfg->db_user) {
            $conn = $this->connectSite($cfg);
            $tablesRaw = DB::connection($conn)->select('SHOW TABLES');
            $tables = array_map(fn($r) => array_values((array)$r)[0], $tablesRaw);
        }

        return view('databases.show', compact('domainModel', 'cfg', 'tables'));
    }

    public function table($domain, $table)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();

        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        abort_if($safeTable !== $table, 400, 'Invalid table name');

        $conn = $this->connectSite($cfg);

        $columns = DB::connection($conn)->select("DESCRIBE `$safeTable`");
        $rows = DB::connection($conn)->table($safeTable)->limit(100)->get();

        $pkRow = collect(DB::connection($conn)->select("SHOW KEYS FROM `$safeTable` WHERE Key_name = 'PRIMARY'"))->first();
        $primaryKey = $pkRow->Column_name ?? null;

        return view('databases.table', compact('domainModel', 'cfg', 'table', 'columns', 'rows', 'primaryKey'));
    }

    public function updateRow(Request $request, $domain, $table, $id)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();

        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        abort_if($safeTable !== $table, 400, 'Invalid table name');

        $conn = $this->connectSite($cfg);

        $pkRow = collect(DB::connection($conn)->select("SHOW KEYS FROM `$safeTable` WHERE Key_name = 'PRIMARY'"))->first();
        $primaryKey = $pkRow->Column_name ?? null;
        abort_if(!$primaryKey, 400, 'Table has no primary key');

        $input = $request->input('row', []);
        if (!is_array($input)) {
            return back()->with('error', 'Invalid row payload');
        }

        DB::connection($conn)->table($safeTable)->where($primaryKey, $id)->update($input);

        DatabaseAudit::create([
            'user_id' => auth()->id(),
            'site_name' => $domainModel->domain,
            'action' => 'row_update',
            'query_text' => "UPDATE `$safeTable` WHERE `$primaryKey` = ".addslashes((string)$id),
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Row updated successfully');
    }

    public function runQuery(Request $request, $domain)
    {
        $request->validate([
            'sql' => 'required|string|max:20000',
        ]);

        $sql = trim($request->sql);

        if (preg_match('/\b(drop|truncate|alter|grant|revoke|create\s+user)\b/i', $sql)) {
            return back()->with('error', 'Blocked dangerous SQL command.');
        }

        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();
        $conn = $this->connectSite($cfg);

        try {
            $result = DB::connection($conn)->select($sql);

            DatabaseAudit::create([
                'user_id' => auth()->id(),
                'site_name' => $domainModel->domain,
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
