@extends('layouts.panel')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
:root {
    --surface-0: #050a14;
    --surface-1: #0a1220;
    --surface-2: #161f32;
    --border-faint: rgba(148, 163, 184, 0.1);
    --cyan: #22d3ee;
    --green: #4ade80;
    --red: #f87171;
    --text-primary: #e2e8f0;
    --text-secondary: #94a3b8;
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
}

body {
    font-family: var(--font-ui);
    background: var(--surface-0);
    color: var(--text-primary);
}

.db-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* HEADER & TITLE */
.db-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-faint);
}

.db-title {
    font-size: 1.5rem;
    font-weight: 600;
}

.db-title span {
    color: var(--cyan);
    font-family: var(--font-mono);
}

/* CARDS & SECTIONS */
.db-card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.section-title {
    font-family: var(--font-mono);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--cyan);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--border-faint);
}

/* FORMS & INPUTS */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

input, textarea {
    background: var(--surface-2);
    border: 1px solid var(--border-faint);
    border-radius: 6px;
    padding: 10px 14px;
    color: white;
    font-family: var(--font-mono);
    font-size: 13px;
    transition: 0.2s;
}

input:focus, textarea:focus {
    outline: none;
    border-color: var(--cyan);
    box-shadow: 0 0 0 2px rgba(34, 211, 238, 0.1);
}

/* BUTTONS */
.btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-family: var(--font-mono);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary { background: var(--cyan); color: #000; }
.btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

.btn-success { background: rgba(74, 222, 128, 0.1); color: var(--green); border: 1px solid var(--green); }
.btn-success:hover { background: var(--green); color: #000; }

/* TABLE */
.db-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.db-table th {
    text-align: left;
    padding: 12px;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-secondary);
    border-bottom: 2px solid var(--border-faint);
}

.db-table td {
    padding: 14px 12px;
    font-size: 14px;
    border-bottom: 1px solid var(--border-faint);
}

.db-table tr:hover { background: rgba(255,255,255,0.02); }

.link-action {
    color: var(--cyan);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
}

.link-action:hover { text-decoration: underline; }

/* RESULT PRE */
.query-result {
    background: linear-gradient(180deg, #020817 0%, #06101f 100%);
    padding: 18px;
    border-radius: 10px;
    border: 1px solid rgba(34, 211, 238, 0.16);
    color: #f8fafc;
    overflow-x: auto;
    font-family: var(--font-mono);
    font-size: 13px;
    line-height: 1.75;
    white-space: pre-wrap;
    word-break: break-word;
    text-shadow: 0 0 1px rgba(255,255,255,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.02);
}
</style>

<div class="db-container">
    <div class="db-header">
        <h2 class="db-title">Website: <span>{{ $domainModel->domain }}</span></h2>
        <a href="{{ route('databases.export', $domainModel->domain) }}" class="btn btn-success">
            <svg style="width:16px; margin-right:8px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            EXPORT SQL
        </a>
    </div>

    @if(session('success'))
        <div style="color:var(--green); background:rgba(74,222,128,0.1); padding:12px; border-radius:6px; margin-bottom:20px; font-size:14px; border-left:4px solid var(--green);">
            {{ session('success') }}
        </div>
    @endif

    <div class="db-card">
        <h3 class="section-title">Database Configuration</h3>
        <form method="POST" action="{{ route('databases.config', $domainModel->domain) }}">
            @csrf
            <div class="form-grid">
                <input name="db_host" value="{{ old('db_host', $cfg->db_host ?? '127.0.0.1') }}" placeholder="Host">
                <input name="db_port" value="{{ old('db_port', $cfg->db_port ?? 3306) }}" placeholder="Port">
                <input name="db_name" value="{{ old('db_name', $cfg->db_name ?? '') }}" placeholder="DB Name">
                <input name="db_user" value="{{ old('db_user', $cfg->db_user ?? '') }}" placeholder="User">
                <input type="password" name="db_password" placeholder="Password (keep blank to skip)">
            </div>
            
            <div style="margin-top:20px; display:flex; align-items:center; justify-content: space-between;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px; color:var(--text-secondary)">
                    <input type="checkbox" name="is_active" value="1" {{ ($cfg->is_active ?? true) ? 'checked' : '' }}>
                    Connection Active
                </label>
                <button type="submit" class="btn btn-primary">SAVE SETTINGS</button>
            </div>
        </form>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="db-card">
            <h3 class="section-title">Import SQL</h3>
            {{-- <form method="POST" action="{{ route('databases.import', $domainModel->domain) }}" enctype="multipart/form-data">
                
                <div style="display:flex; flex-direction:column; gap:15px;">
                    <input type="file" >
                    <button type="submit" class="btn btn-primary" style="width:100%">START IMPORT</button>
                </div>
            </form> --}}
            <form method="POST" action="{{ route('databases.import', $domainModel->domain) }}" enctype="multipart/form-data" style="height:100%; display:flex; flex-direction:column;">
                @csrf
                <div style="flex:1; display:flex; flex-direction:column; gap:15px;">
                    <input type="file" name="sql_file" accept=".sql,.txt" required style="width:100%">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">
                    START IMPORT
                </button>
            </form>
        </div>

        <div class="db-card">
            <h3 class="section-title">SQL Console</h3>
            {{-- <form method="POST" action="{{ route('databases.query', $domainModel->domain) }}">
               
                
                <button type="submit" class="btn btn-primary" style="width:100%">EXECUTE QUERY</button>
            </form> --}}
            <form method="POST" action="{{ route('databases.query', $domainModel->domain) }}" style="height:100%; display:flex; flex-direction:column;">
                @csrf
                <div style="flex:1;">
                    <textarea name="sql" rows="3" style="width:100%; margin-bottom:15px; resize:none;" placeholder="SELECT * FROM users LIMIT 10;">{{ old('sql') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">
                    EXECUTE QUERY
                </button>
            </form>
        </div>
    </div>

    @if(session('query_result'))
        <div class="db-card">
            <h3 class="section-title">Query Result</h3>
            <pre class="query-result">{{ json_encode(session('query_result'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif

    <div class="db-card">
        <h3 class="section-title">Tables List</h3>
        @if(empty($tables))
            <p style="text-align:center; padding:20px; color:var(--text-secondary); font-family:var(--font-mono)">// NO TABLES FOUND OR DB DISCONNECTED</p>
        @else
            <table class="db-table">
                <thead>
                    <tr>
                        <th>TABLE NAME</th>
                        <th>RECORDS</th>
                        <th style="text-align:right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tables as $t)
                    <tr>
                        <td style="font-family:var(--font-mono); font-weight:600">{{ $t }}</td>
                        <td style="color:var(--text-secondary); font-size:12px;">--</td>
                        <td style="text-align:right">
                            <a href="{{ route('databases.table', [$domainModel->domain, $t]) }}" class="link-action" style="margin-right:15px">Browse</a>
                            <a href="{{ route('databases.structure', [$domainModel->domain, $t]) }}" class="link-action">Structure</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection