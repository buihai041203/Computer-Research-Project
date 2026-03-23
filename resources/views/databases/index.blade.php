@extends('layouts.panel')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
:root {
    --surface-0: #050a14;
    --surface-1: #0a1220;
    --surface-2: #161f32;
    --border-faint: rgba(148, 163, 184, 0.08);
    --cyan: #22d3ee;
    --cyan-glow: rgba(34, 211, 238, 0.3);
    --green: #4ade80;
    --red: #f87171;
    --text-primary: #f1f5f9;
    --text-secondary: #94a3b8;
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
    --r-lg: 16px;
}

/* TỔNG THỂ ĐỒNG BỘ NỀN GRADIENT */
html, body {
    font-family: var(--font-ui);
    background: radial-gradient(circle at top left, #0f172a, #050a14) !important;
    color: var(--text-primary);
    min-height: 100vh;
    margin: 0;
}

.scc-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 30px 24px;
}

/* TIÊU ĐỀ ĐỒNG BỘ VỚI VẠCH MÀU CYAN */
.page-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 30px;
    letter-spacing: -0.02em;
    /* border-left: 4px solid var(--cyan); */
    padding-left: 0px;
    text-shadow: 0 0 15px var(--cyan-glow);
}

/* CARD ĐỒNG BỘ HIỆU ỨNG ĐỔ BÓNG */
.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
}

/* TABLE DÁNG CHUẨN */
.dtable {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

/* HEADER BẢNG ĐỒNG BỘ */
.dtable thead th {
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #CCFFFF;
    padding: 16px;
    background: rgba(15, 23, 42, 0.9);
    border-bottom: 2px solid rgba(34, 211, 238, 0.1);
    border-right: 1px solid var(--border-faint);
    text-align: left;
}

/* DÒNG & HOVER */
.dtable tbody tr {
    transition: all 0.2s ease;
}

.dtable tbody tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.01);
}

.dtable tbody tr:hover {
    background: rgba(34, 211, 238, 0.04) !important;
    backdrop-filter: blur(4px);
}

.dtable td {
    padding: 14px 16px;
    font-size: 14px;
    border-bottom: 1px solid var(--border-faint);
    border-right: 1px solid rgba(255, 255, 255, 0.02);
}

.dtable td:last-child, .dtable th:last-child {
    border-right: none;
}

/* BADGE PHÁT SÁNG */
.badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    text-shadow: 0 0 5px currentColor;
}

.badge--on {
    background: rgba(74, 222, 128, 0.1);
    color: var(--green);
    border: 1px solid rgba(74, 222, 128, 0.2);
}

.badge--off {
    background: rgba(248, 113, 113, 0.1);
    color: var(--red);
    border: 1px solid rgba(248, 113, 113, 0.2);
}

/* NÚT BẤM NEON */
.btn-open {
    padding: 6px 14px;
    border-radius: 8px;
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 700;
    background: var(--cyan);
    color: #000;
    border: none;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0 0 10px var(--cyan-glow);
}

.btn-open:hover {
    background: #fff;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
}

.t-mono { font-family: var(--cyan); font-size: 13px; }
</style>

<div class="scc-wrap">

    <h1 class="page-title">Databases</h1>

    @if(session('success'))
        <div style="color:var(--green); background:rgba(74,222,128,0.1); padding:10px; border-radius:6px; margin-bottom:15px; font-family:var(--font-mono); font-size:12px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="color:var(--red); background:rgba(248,113,113,0.1); padding:10px; border-radius:6px; margin-bottom:15px; font-family:var(--font-mono); font-size:12px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <table class="dtable">
            <thead>
                <tr>
                    <th>SITE</th>
                    <th>DB NAME</th>
                    <th>HOST</th>
                    <th>PORT</th>
                    <th style=>STATUS</th>
                    <th style=>ACTION</th>
                </tr>
            </thead>
            <tbody>
            @forelse($domains as $d)
                @php $cfg = $d->databaseConfig; @endphp
                <tr>
                    <td style="font-weight: 600;">{{ $d->domain }}</td>
                    <td class="t-mono">{{ $cfg->db_name ?? 'NOT CONFIGURED' }}</td>
                    <td class="t-mono" style="color: var(--text-secondary);">{{ $cfg->db_host ?? '-' }}</td>
                    <td class="t-mono">{{ $cfg->db_port ?? '-' }}</td>
                    <td style="text-align: left;">
                        @if($cfg && $cfg->is_active)
                            <span class="badge badge--on">ON</span>
                        @else
                            <span class="badge badge--off">OFF</span>
                        @endif
                    </td>
                    <td style="text-align: left;">
                        <a href="{{ route('databases.show', $d->domain) }}" class="btn-open">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; font-family:var(--font-mono); color:var(--text-secondary)">
                        // NO DATABASES FOUND
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection