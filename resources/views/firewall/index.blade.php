@extends('layouts.panel')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>

/* ===== DESIGN TOKENS ===== */
:root {
    --surface-0: #060c17;
    --surface-1: #0a1220;
    --surface-2: #0f1a2e;

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

/* ===== BASE ===== */
html, body {
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
    color: var(--cyan);
    font-style: normal;
}

/* ===== CARD ===== */
.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    overflow: hidden;
}

/* ===== TABLE ===== */
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

/* ===== INPUT ===== */
.input-cyber {
    flex-grow: 1; 
    background: var(--surface-2);
    border: 1px solid var(--border-faint);
    color: var(--text-primary);
    padding: 12px 16px; 
    border-radius: 6px;
    font-family: var(--font-mono);
    font-size: 13px; 
    outline: none;
    transition: border-color 0.2s;
}

.input-cyber:focus {
    border-color: var(--cyan); 
}

/* ===== BUTTON ===== */
.btn-danger {
    background: rgba(248,113,113,.15);
    color: var(--red);
    border: 1px solid rgba(248,113,113,.3);
    padding: 8px 14px;
    border-radius: 6px;
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 700;
}
.btn-danger:hover {
    background: rgba(248,113,113,.25);
}

.btn-success {
    color: var(--green);
    font-family: var(--font-mono);
    font-weight: 700;
}

/* MONO */
.t-mono {
    font-family: var(--font-mono);
}

</style>

<div class="scc-wrap">

    <h1 class="page-title">
        Firewall <em>Control</em>
    </h1>

    {{-- FORM --}}
    <div class="card" style="padding:20px; margin-bottom:20px;">
        <form method="POST" action="/firewall/block">
            @csrf

            <div style="display:flex; gap:10px;">

                <input name="ip" class="input-cyber" placeholder="IP address">

                <input name="reason" class="input-cyber" placeholder="Reason">

                <button class="btn-danger">
                    BLOCK
                </button>

            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <table class="dtable">

            <thead>
                <tr>
                    <th>IP ADDRESS</th>
                    <th>REASON</th>
                    <th>ACTION</th>
                </tr>
            </thead>

            <tbody>
                @forelse($ips as $ip)
                <tr>

                    <td class="t-mono" style="color:var(--cyan)">
                        {{ $ip->ip }}
                    </td>

                    <td style="color:var(--text-secondary)">
                        {{ $ip->reason }}
                    </td>

                    <td>
                        <form method="POST" action="/firewall/{{ $ip->id }}">
                            @csrf
                            @method('DELETE')

                            <button class="btn-success">
                                UNBLOCK
                            </button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center; padding:30px; font-family:var(--font-mono); color:var(--text-secondary)">
                        // NO BLOCKED IPS
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>

@endsection