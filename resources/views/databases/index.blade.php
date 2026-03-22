@extends('layouts.panel')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
:root {
    --surface-0: #060c17;
    --surface-1: #0a1220;
    --border-faint: rgba(148,163,184,.07);
    --cyan: #22d3ee;
    --green: #4ade80;
    --red: #f87171;
    --text-primary: #e2e8f0;
    --text-secondary: #64748b;
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
    --r-lg: 14px;
}
.t-mono {
    font-family: var(--font-mono);
}
/* CARD */
.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.35);
}

/* TABLE */
.dtable {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

/* HEADER */
.dtable thead th {
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    color: var(--text-secondary);
    padding: 12px 16px;
    background: rgba(255,255,255,0.02);
    border-bottom: 1px solid var(--border-faint);
}

/* ROW */
.dtable tbody tr {
    border-bottom: 1px solid var(--border-faint);
    transition: 0.2s;
}

/* ZEBRA */
.dtable tbody tr:nth-child(even) {
    background: rgba(255,255,255,0.01);
}

/* HOVER */
.dtable tbody tr:hover {
    background: rgba(34,211,238,.04);
}

/* CELL */
.dtable td {
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
}
/* VERTICAL LINES (QUAN TRỌNG) */
.dtable th,
.dtable td {
    border-right: 1px solid var(--border-faint);
}

.dtable th:last-child,
.dtable td:last-child {
    border-right: none;
}

/* BO GÓC CHUẨN */
.dtable thead th:first-child {
    border-top-left-radius: 14px;
}
.dtable thead th:last-child {
    border-top-right-radius: 14px;
}
.dtable th,
.dtable td {
    text-align: left !important;
}
.dtable td:last-child {
    text-align: left !important;
}

/* STATUS BADGE */
.badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
}

.badge--on {
    background: rgba(74,222,128,.1);
    color: var(--green);
    border: 1px solid rgba(74,222,128,.2);
}

.badge--off {
    background: rgba(248,113,113,.1);
    color: var(--red);
    border: 1px solid rgba(248,113,113,.2);
}

/* ACTION BUTTON */
.btn-open {
    padding: 4px 10px;
    border-radius: var(--r-sm);
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    background: rgba(34,211,238,.1);
    color: var(--cyan);
    border: 1px solid rgba(34,211,238,.25);
    text-decoration: none;
}

.btn-open:hover {
    background: rgba(34,211,238,.2);
}

/* PAGE TITLE */
.page-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 20px;
}
html, body {
    font-family: var(--font-ui);
    background: var(--surface-0);
    color: var(--text-primary);
}
.scc-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px;
}
</style>

<div class="scc-wrap">

    <h1 class="page-title">Databases</h1>

    @if(session('success'))
        <div style="color:var(--green); margin-bottom:10px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="color:var(--red); margin-bottom:10px;">{{ session('error') }}</div>
    @endif

    <div class="card">
        <table class="dtable">
            <thead>
                <tr>
                    <th>SITE</th>
                    <th>DB NAME</th>
                    <th>HOST</th>
                    <th>PORT</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
            @forelse($domains as $d)
                @php $cfg = $d->databaseConfig; @endphp
                <tr>
                    <td>{{ $d->domain }}</td>
                    <td style="font-family: var(--font-mono);">{{ $cfg->db_name ?? 'chưa cấu hình' }}</td>
                    <td>{{ $cfg->db_host ?? '-' }}</td>
                    <td class="t-mono">{{ $cfg->db_port ?? '-' }}</td>
                    <td>
                        @if($cfg && $cfg->is_active)
                            <span class="badge badge--on">ON</span>
                        @else
                            <span class="badge badge--off">OFF</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('databases.show', $d->domain) }}" class="btn-open">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:30px; font-family:var(--font-mono); color:var(--text-secondary)">
                        // NO WEBSITE FOUND
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
