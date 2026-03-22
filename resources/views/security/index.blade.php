@extends('layouts.panel')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
/* ===== DESIGN TOKENS ===== */
:root {
    --surface-0: #060c17;
    --surface-1: #0a1220;

    --border-faint: rgba(148,163,184,.07);
    --border-subtle: rgba(148,163,184,.13);

    --cyan: #22d3ee;
    --red: #f87171;

    --text-primary: #e2e8f0;
    --text-secondary: #64748b;
    --text-muted: #2d3f5c;

    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;

    --r-lg: 14px;
}

/* ===== BASE ===== */
body {
    font-family: var(--font-ui);
    background: var(--surface-0);
    color: var(--text-primary);
}

/* ===== WRAP ===== */
.scc-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px;
}

/* ===== TITLE ===== */
.page-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 20px;
    letter-spacing: -0.025em;
}
.page-title em {
    font-style: normal;
    color: var(--cyan);
}

/* ===== CARD ===== */
.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.35);
}
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

/* MONO */
.t-mono {
    font-family: var(--font-mono);
    
    font-size: 12px;
    font-weight: 500;
}
.badge {
    font-weight: 700;
    font-size: 10px;
}

/* TYPE BADGE */
.badge-danger {
    color: var(--red);
    font-weight: 700;
    font-family: var(--font-mono);
}
</style>

<div class="scc-wrap">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
        <h1 class="page-title" style="margin-bottom:0;">
            Security <em>Events</em>
        </h1>

        <form method="POST" action="{{ route('security.clear') }}" onsubmit="return confirm('Xóa toàn bộ security logs?');">
            @csrf
            <button type="submit" style="padding:7px 12px; border-radius:8px; border:1px solid rgba(248,113,113,.35); background:rgba(248,113,113,.10); color:var(--red); font-family:var(--font-mono); font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; cursor:pointer;">
                Clear Logs
            </button>
        </form>
    </div>

    @if(session('success'))
        <div style="color:var(--cyan); margin-bottom:10px; font-family:var(--font-mono); font-size:11px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="color:var(--red); margin-bottom:10px; font-family:var(--font-mono); font-size:11px;">{{ session('error') }}</div>
    @endif

    <div class="card">
        <table class="dtable">
            <thead>
                <tr>
                    <th>IP ADDRESS</th>
                    <th>TYPE</th>
                    <th>DESCRIPTION</th>
                    <th>TIME</th>
                </tr>
            </thead>

            <tbody>
                @forelse($events as $event)
                <tr>
                    <td class="t-mono">{{ $event->ip }}</td>

                    <td>
                        <span class="badge-danger">
                            {{ strtoupper($event->type) }}
                        </span>
                    </td>

                    <td style="color: var(--text-secondary)">
                        {{ $event->description }}
                    </td>

                    <td class="t-mono" style="color: var(--text-secondary)">
                        {{ $event->created_at }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:30px; font-family:var(--font-mono); color:var(--text-muted)">
                        // NO SECURITY EVENTS
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection