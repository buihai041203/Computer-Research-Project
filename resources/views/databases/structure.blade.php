@extends('layouts.panel')

@section('content')
<style>
/* Tái định nghĩa các biến để đồng bộ */
:root {
    --surface-0: #050a14;
    --surface-1: #0a1220;
    --surface-2: #161f32;
    --border-faint: rgba(148, 163, 184, 0.1);
    --cyan: #22d3ee;
    --green: #4ade80;
    --blue: #0ea5e9;
    --text-primary: #e2e8f0;
    --text-secondary: #94a3b8;
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
}

/* Reset nền và font cho toàn bộ trang */
body {
    background: var(--surface-0);
    color: var(--text-primary);
    font-family: var(--font-ui);
    margin: 0;
}

/* Container chính */
div[style*="padding:20px"] {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px !important;
}

/* Tiêu đề chính */
h2 {
    font-size: 1.5rem;
    font-weight: 600;
    border-bottom: 1px solid var(--border-faint);
    padding-bottom: 15px;
    margin-bottom: 25px;
}

/* Custom tiêu đề bảng */
h3 {
    font-family: var(--font-mono);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--cyan);
    margin-top: 30px !important;
    display: flex;
    align-items: center;
    gap: 10px;
}

h3::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--border-faint);
}

/* Thanh điều hướng (Buttons) */
div[style*="display:flex; gap:12px"] a, 
div[style*="display:flex; gap:12px"] span {
    font-family: var(--font-mono);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    transition: 0.3s;
    border-width: 1px !important;
}

/* Nút Back */
a[href*="databases.show"] {
    background: transparent;
    border-color: var(--border-faint) !important;
    color: var(--text-secondary) !important;
}
a[href*="databases.show"]:hover {
    background: var(--surface-2);
    color: white !important;
}

/* Nút Browse */
a[href*="table"] {
    border-color: var(--blue) !important;
    color: var(--blue) !important;
}
a[href*="table"]:hover {
    background: var(--blue);
    color: #000 !important;
}

/* Badge Structure hiện tại */
span[style*="color:#16a34a"] {
    background: rgba(74, 222, 128, 0.1);
    border-color: var(--green) !important;
}

/* TABLE STYLING - Quan trọng nhất */
table {
    width: 100%;
    border-collapse: collapse;
    background: var(--surface-1);
    border: 1px solid var(--border-faint) !important;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 15px;
}

table thead tr {
    background: rgba(255, 255, 255, 0.02);
}

table th {
    text-align: left;
    padding: 12px !important;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-secondary);
    border-bottom: 2px solid var(--border-faint) !important;
    border-right: none !important;
    border-left: none !important;
    border-top: none !important;
    text-transform: uppercase;
}

table td {
    padding: 14px 12px !important;
    font-size: 13px;
    border-bottom: 1px solid var(--border-faint) !important;
    border-right: none !important;
    border-left: none !important;
    font-family: var(--font-mono);
}

table tr:last-child td {
    border-bottom: none !important;
}

table tr:hover {
    background: rgba(34, 211, 238, 0.03);
}

/* Cột "Field" nổi bật hơn */
table td:first-child {
    color: var(--cyan);
    font-weight: 600;
}

/* Khối CREATE TABLE (SQL Highlight) */
pre {
    background: #010409 !important; /* Đen sâu hơn */
    border: 1px solid var(--border-faint);
    padding: 20px !important;
    font-family: var(--font-mono) !important;
    font-size: 13px !important;
    line-height: 1.6;
    color: #8b949e !important;
    box-shadow: inset 0 2px 10px rgba(0,0,0,0.3);
}
</style>
<div style="padding:20px;">
    <h2>{{ $domainModel->domain }} / {{ $table }} / Structure</h2>

    <div style="margin:10px 0 16px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <a href="{{ route('databases.show', $domainModel->domain) }}" style="padding:6px 10px; border:1px solid #ccc; border-radius:6px; text-decoration:none;">← Back to database</a>
        <a href="{{ route('databases.table', [$domainModel->domain, $table]) }}" style="padding:6px 10px; border:1px solid #0ea5e9; border-radius:6px; text-decoration:none; color:#0ea5e9;">Browse</a>
        <span style="padding:6px 10px; border:1px solid #16a34a; border-radius:6px; color:#16a34a; font-weight:600;">Structure</span>
    </div>

    <h3 style="margin-top:16px;">Columns</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
        <thead>
            <tr>
                <th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>
            </tr>
        </thead>
        <tbody>
        @foreach($columns as $c)
            <tr>
                <td>{{ $c->Field }}</td>
                <td>{{ $c->Type }}</td>
                <td>{{ $c->Null }}</td>
                <td>{{ $c->Key }}</td>
                <td>{{ $c->Default }}</td>
                <td>{{ $c->Extra }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3 style="margin-top:16px;">Indexes</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
        <thead>
            <tr>
                <th>Key</th><th>Column</th><th>Unique</th><th>Index Type</th>
            </tr>
        </thead>
        <tbody>
        @foreach($indexes as $i)
            <tr>
                <td>{{ $i->Key_name }}</td>
                <td>{{ $i->Column_name }}</td>
                <td>{{ $i->Non_unique == 0 ? 'YES' : 'NO' }}</td>
                <td>{{ $i->Index_type }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3 style="margin-top:16px;">CREATE TABLE</h3>
    <pre style="white-space:pre-wrap; background:#0b1220; color:#ddd; padding:12px; border-radius:8px;">{{ $ddl }}</pre>
</div>
@endsection
