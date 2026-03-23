<?php

namespace App\Http\Controllers;

use App\Models\DatabaseAudit;
use App\Models\Domain;
use App\Models\SiteDatabase;
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
            $cfg->db_password = $data['db_password'];
        }

        $cfg->is_active = (bool) ($data['is_active'] ?? true);
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
            $tables = array_map(fn($r) => array_values((array) $r)[0], $tablesRaw);
        }

        return view('databases.show', compact('domainModel', 'cfg', 'tables'));
    }

    public function table($domain, $table)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();

        $safeTable = $this->sanitizeTable($table);
        $conn = $this->connectSite($cfg);

        $columns = DB::connection($conn)->select("DESCRIBE `$safeTable`");
        $rows = DB::connection($conn)->table($safeTable)->limit(100)->get();

        $pkRow = collect(DB::connection($conn)->select("SHOW KEYS FROM `$safeTable` WHERE Key_name = 'PRIMARY'"))->first();
        $primaryKey = $pkRow->Column_name ?? null;

        return view('databases.table', compact('domainModel', 'cfg', 'table', 'columns', 'rows', 'primaryKey'));
    }

    public function structure($domain, $table)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();
        $safeTable = $this->sanitizeTable($table);
        $conn = $this->connectSite($cfg);

        $columns = DB::connection($conn)->select("SHOW FULL COLUMNS FROM `$safeTable`");
        $indexes = DB::connection($conn)->select("SHOW INDEX FROM `$safeTable`");
        $createTable = DB::connection($conn)->select("SHOW CREATE TABLE `$safeTable`");
        $ddl = $createTable[0]->{'Create Table'} ?? '';

        return view('databases.structure', compact('domainModel', 'cfg', 'table', 'columns', 'indexes', 'ddl'));
    }

    public function designer($domain)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();
        $conn = $this->connectSite($cfg);

        $tablesRaw = DB::connection($conn)->select('SHOW TABLES');
        $tables = array_map(fn($r) => array_values((array) $r)[0], $tablesRaw);

        $relations = DB::connection($conn)->select(
            'SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$cfg->db_name]
        );

        return view('databases.designer', compact('domainModel', 'cfg', 'tables', 'relations'));
    }

    public function updateRow(Request $request, $domain, $table, $id)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();

        $safeTable = $this->sanitizeTable($table);
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
            'query_text' => "UPDATE `$safeTable` WHERE `$primaryKey` = " . addslashes((string) $id),
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

    public function export(Request $request, $domain)
    {
        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();

        $file = storage_path('app/tmp_export_' . $domainModel->domain . '_' . now()->format('Ymd_His') . '.sql');

        $cmd = sprintf(
            'MYSQL_PWD=%s mysqldump -h %s -P %s -u %s %s > %s',
            escapeshellarg($cfg->db_password ?? ''),
            escapeshellarg($cfg->db_host),
            escapeshellarg((string) $cfg->db_port),
            escapeshellarg($cfg->db_user),
            escapeshellarg($cfg->db_name),
            escapeshellarg($file)
        );

        exec($cmd . ' 2>&1', $out, $code);
        if ($code !== 0 || !file_exists($file)) {
            return back()->with('error', 'Export failed: ' . implode("\n", $out));
        }

        return response()->download($file, $domainModel->domain . '_database.sql')->deleteFileAfterSend(true);
    }

    public function import(Request $request, $domain)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:20480',
        ]);

        $domainModel = Domain::where('domain', $domain)->firstOrFail();
        $cfg = SiteDatabase::where('domain_id', $domainModel->id)->firstOrFail();
        $conn = $this->connectSite($cfg);

        $sql = file_get_contents($request->file('sql_file')->getRealPath());
        if ($sql === false || trim($sql) === '') {
            return back()->with('error', 'Invalid SQL file');
        }

        try {
            DB::connection($conn)->unprepared($sql);

            DatabaseAudit::create([
                'user_id' => auth()->id(),
                'site_name' => $domainModel->domain,
                'action' => 'import',
                'query_text' => 'Imported SQL file: ' . $request->file('sql_file')->getClientOriginalName(),
                'ip' => $request->ip(),
            ]);

            return back()->with('success', 'Import SQL thành công');
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function sanitizeTable(string $table): string
    {
        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        abort_if($safeTable !== $table, 400, 'Invalid table name');
        return $safeTable;
    }

    private function connectSite(SiteDatabase $cfg): string
    {
        $name = 'site_runtime_' . $cfg->id;

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
