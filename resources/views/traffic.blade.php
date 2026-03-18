@extends('layouts.panel')

@section('content')

<style>
/* ===== ĐỒNG BỘ DARK GRID UI ===== */
:root {
    --bg-main: #020617;
    --panel-bg: rgba(15, 23, 42, 0.8);
    --accent-blue: #38bdf8;
    --accent-green: #10b981;
    --accent-red: #f43f5e;
    --text-bright: #f8fafc;
    --text-dim: #94a3b8;
    --border-color: rgba(255, 255, 255, 0.1);
}

body {
    background-color: var(--bg-main) !important;
    background-image:
        radial-gradient(circle at 50% -20%, rgba(56,189,248,0.15), transparent),
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
    background-size: 100% 100%, 40px 40px, 40px 40px;
    font-family: 'Inter', sans-serif;
    color: var(--text-bright);
}

/* TIÊU ĐỀ ĐỒNG BỘ */
.page-title {
    font-size: 1.9rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    background: linear-gradient(90deg, #ffffff 0%, #0284c7 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 25px;
}

/* THIẾT KẾ CARD KÍNH MỜ */
.table-card {
    background: var(--panel-bg) !important;
    backdrop-filter: blur(12px);
    border: 1px solid var(--border-color) !important;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
}

/* BẢNG TÁCH DÒNG HIỆN ĐẠI */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
}

thead th {
    text-align: left;
    font-weight: 700;
    color: var(--accent-blue);
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 10px 16px;
    border: none;
}

tbody td {
    padding: 16px;
    font-size: 0.9rem;
    background: rgba(255, 255, 255, 0.03);
    border: none !important;
}

tbody td:first-child { border-radius: 12px 0 0 12px; }
tbody td:last-child { border-radius: 0 12px 12px 0; }

tbody tr:hover td {
    background: rgba(56, 189, 248, 0.1);
    color: #fff;
}

/* STYLE CHI TIẾT */
.ip {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
    color: var(--accent-blue);
}

.country {
    color: var(--text-bright);
}

/* BADGES NEON */
.badge {
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.badge-human {
    background: rgba(16, 185, 129, 0.1);
    color: var(--accent-green);
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-bot {
    background: rgba(244, 63, 94, 0.1);
    color: var(--accent-red);
    border: 1px solid rgba(244, 63, 94, 0.3);
}

.time {
    color: var(--text-dim);
    font-size: 0.8rem;
}
</style>

<div class="mb-8">
    <h1 class="page-title">Traffic Logs</h1>
    <p class="text-dim text-sm" style="margin-top: -20px;">Detailed history of server requests and visitor identities</p>
</div>

<div class="table-card">
    <table class="w-full">
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Country</th>
                <th>Visitor Type</th>
                <th style="text-align: right;">Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visitors as $v)
            <tr>
                <td class="ip">{{ $v->ip }}</td>
                <td class="country">🌍 {{ $v->country }}</td>
                <td>
                    @if($v->is_bot)
                        <span class="badge badge-bot"> Bot</span>
                    @else
                        <span class="badge badge-human"> Human</span>
                    @endif
                </td>
                <td class="time" style="text-align: left;">
                    {{ $v->created_at->diffForHumans() }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection