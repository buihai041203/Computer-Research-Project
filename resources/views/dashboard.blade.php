@extends('layouts.panel')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     SECURITY COMMAND CENTER  ·  v2.0
     Stack: ApexCharts · jsVectorMap · Space Mono + DM Sans
     Architecture: token-driven design system, modular JS modules,
     centralised API service, XSS-safe DOM renderer, toast system.
══════════════════════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">

<style>
/* ─────────────────────────────────────────────────────
   §1  DESIGN TOKENS
───────────────────────────────────────────────────── */
:root {
    /* Surface */
    --surface-0: #060c17;
    --surface-1: #0a1220;
    --surface-2: #0f1a2e;
    --surface-3: #162236;

    /* Borders */
    --border-faint:   rgba(148,163,184,.07);
    --border-subtle:  rgba(148,163,184,.13);
    --border-strong:  rgba(148,163,184,.28);

    /* Accent ramps */
    --cyan:       #22d3ee;
    --cyan-dim:   rgba(34,211,238,.1);
    --cyan-mid:   rgba(34,211,238,.22);
    --green:      #4ade80;
    --green-dim:  rgba(74,222,128,.1);
    --red:        #f87171;
    --red-dim:    rgba(248,113,113,.1);
    --amber:      #fbbf24;
    --amber-dim:  rgba(251,191,36,.1);
    --violet:     #a78bfa;
    --violet-dim: rgba(167,139,250,.1);

    /* Text */
    --text-primary:   #e2e8f0;
    --text-secondary: #64748b;
    --text-muted:     #2d3f5c;
    --text-accent:    var(--cyan);

    /* Fonts */
    --font-ui:   'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;

    /* Radii */
    --r-sm: 6px;
    --r-md: 10px;
    --r-lg: 14px;
    --r-xl: 20px;

    /* Motion */
    --ease: cubic-bezier(.4,0,.2,1);
    --dur: 180ms;
}


/* ─────────────────────────────────────────────────────
   §2  RESET + BASE
───────────────────────────────────────────────────── */
*,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: var(--font-ui);
    background: var(--surface-0);
    background-image:
        radial-gradient(ellipse 70% 40% at 20% 0%,  rgba(34,211,238,.05) 0%, transparent 55%),
        radial-gradient(ellipse 50% 35% at 80% 100%, rgba(167,139,250,.04) 0%, transparent 55%);
    color: var(--text-primary);
    min-height: 100vh;
    padding: 24px;
    line-height: 1.5;
}

/* ─────────────────────────────────────────────────────
   §3  LAYOUT PRIMITIVES
───────────────────────────────────────────────────── */
.scc-wrap { max-width: 1440px; margin: 0 auto; }

/* 8-pt spacing grid */
.g-3  { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 16px; }
.g-2  { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; margin-bottom: 16px; }
.g-41 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.g-1  { margin-bottom: 16px; }

/* ─────────────────────────────────────────────────────
   §4  CARD SHELL
───────────────────────────────────────────────────── */
.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    transition: border-color var(--dur) var(--ease), box-shadow var(--dur) var(--ease);
    overflow: hidden;
}
.card:hover {
    border-color: var(--border-subtle);
    box-shadow: 0 0 0 1px var(--border-faint), 0 8px 32px rgba(0,0,0,.35);
}

.card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-faint);
}

.card__body { padding: 20px; }
.card__body--flush { padding: 0; }

/* ─────────────────────────────────────────────────────
   §5  TYPOGRAPHY SCALE
───────────────────────────────────────────────────── */
.t-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--text-secondary);
}
.t-section {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
}
.t-mono { font-family: var(--font-mono); font-size: 11px; }
.t-dim  { color: var(--text-secondary); }

/* ─────────────────────────────────────────────────────
   §6  STAT CARDS  (top row)
───────────────────────────────────────────────────── */
.stat {
    position: relative;
    padding: 22px 20px;
    overflow: hidden;
}
.stat::after {
    /* top accent line */
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 2px;
}
.stat--cyan::after   { background: var(--cyan);  box-shadow: 0 0 12px var(--cyan); }
.stat--green::after  { background: var(--green); box-shadow: 0 0 12px var(--green); }
.stat--red::after    { background: var(--red);   box-shadow: 0 0 12px var(--red); }

.stat__orb {
    position: absolute;
    top: -20px; right: -20px;
    width: 110px; height: 110px;
    border-radius: 50%;
    opacity: .07;
    filter: blur(28px);
    pointer-events: none;
}
.stat--cyan  .stat__orb { background: var(--cyan); }
.stat--green .stat__orb { background: var(--green); }
.stat--red   .stat__orb { background: var(--red); }

.stat__value {
    font-family: var(--font-mono);
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin: 10px 0 6px;
    letter-spacing: -.02em;
}
.stat--cyan  .stat__value { color: var(--cyan); }
.stat--green .stat__value { color: var(--green); }
.stat--red   .stat__value { color: var(--red); }

.stat__sub {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--text-secondary);
}

/* Skeleton pulse */
.skeleton {
    background: linear-gradient(90deg, var(--surface-2) 25%, var(--surface-3) 50%, var(--surface-2) 75%);
    background-size: 200% 100%;
    animation: skel 1.4s ease-in-out infinite;
    border-radius: 4px;
}
@keyframes skel {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.skel-line { height: 10px; margin: 4px 0; }

/* ─────────────────────────────────────────────────────
   §7  GAUGE BARS  (cpu / ram)
───────────────────────────────────────────────────── */
.gauge { padding: 20px; }

.gauge__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 12px;
}
.gauge__value {
    font-family: var(--font-mono);
    font-size: 1.5rem;
    font-weight: 700;
}
.gauge--cyan   .gauge__value { color: var(--cyan); }
.gauge--violet .gauge__value { color: var(--violet); }

.gauge__status {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.track {
    height: 4px;
    background: var(--surface-3);
    border-radius: 99px;
    overflow: hidden;
    position: relative;
}
.track__fill {
    height: 100%;
    border-radius: 99px;
    transition: width .7s var(--ease);
    position: relative;
}
.track__fill::after {
    content: '';
    position: absolute;
    right: 0; top: 0;
    width: 16px; height: 100%;
    background: rgba(255,255,255,.45);
    filter: blur(3px);
    border-radius: 99px;
}
.gauge--cyan   .track__fill { background: linear-gradient(90deg, rgba(34,211,238,.3), var(--cyan)); }
.gauge--violet .track__fill { background: linear-gradient(90deg, rgba(167,139,250,.3), var(--violet)); }

.gauge__meta {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-family: var(--font-mono);
    font-size: 9px;
    color: var(--text-muted);
}

/* ─────────────────────────────────────────────────────
   §8  LIVE INDICATOR
───────────────────────────────────────────────────── */
.live {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.live__dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--green);
    box-shadow: 0 0 6px var(--green);
    animation: pulse 2.2s ease-in-out infinite;
}
@keyframes pulse {
    0%,100% { opacity:.6; transform: scale(.85); }
    50%      { opacity: 1; transform: scale(1.2); }
}

/* ─────────────────────────────────────────────────────
   §9  DATA TABLE
───────────────────────────────────────────────────── */
.dtable {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

/* HEADER kiểu Excel */
.dtable thead th {
    font-family: var(--font-mono);
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-secondary);
    padding: 12px 15px;
    background: rgba(255,255,255,0.02);
    border-bottom: 2px solid var(--border-faint);
    border-right: 1px solid var(--border-faint);
    text-align: left;
}

/* ROW */
.dtable tbody tr {
    border-bottom: 1px solid var(--border-faint);
    transition: .15s;
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
    padding: 12px 15px;
    font-size: 13px;
    color: var(--text-primary);
    border-right: 1px solid var(--border-faint);
}

/* BỎ border cột cuối */
.dtable td:last-child,
.dtable th:last-child {
    border-right: none;
}

/* MONO COLUMNS (IP / time giống log) */
.dtable .td-mono {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--cyan);
}

/* Empty state */
.empty-cell {
    text-align: center;
    padding: 36px !important;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-muted);
}

/* ─────────────────────────────────────────────────────
   §10  BADGES
───────────────────────────────────────────────────── */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: var(--r-sm);
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .03em;
    white-space: nowrap;
}
.badge--bot   { background: var(--red-dim);   color: var(--red);   border: 1px solid rgba(248,113,113,.2); }
.badge--human { background: var(--green-dim); color: var(--green); border: 1px solid rgba(74,222,128,.2); }

.threat {
    display: inline-block;
    padding: 2px 8px;
    border-radius: var(--r-sm);
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .06em;
}
.threat--CRITICAL { background: var(--red);       color: #0a0a0a; box-shadow: 0 0 10px rgba(248,113,113,.4); }
.threat--HIGH     { background: var(--red-dim);   color: var(--red);   border: 1px solid rgba(248,113,113,.25); }
.threat--MEDIUM   { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(251,191,36,.25); }
.threat--LOW      { background: var(--green-dim); color: var(--green); border: 1px solid rgba(74,222,128,.25); }

/* ─────────────────────────────────────────────────────
   §11  ACTION BUTTON
───────────────────────────────────────────────────── */
.btn-block {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: var(--r-sm);
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    background: var(--red-dim);
    color: var(--red);
    border: 1px solid rgba(248,113,113,.22);
    cursor: pointer;
    transition: all var(--dur) var(--ease);
    letter-spacing: .04em;
}
.btn-block:hover {
    background: rgba(248,113,113,.2);
    border-color: var(--red);
    box-shadow: 0 0 10px rgba(248,113,113,.15);
}

/* ─────────────────────────────────────────────────────
   §12  ATTACK MAP
───────────────────────────────────────────────────── */
#attackMap {
    height: 360px;
    border-radius: var(--r-md);
    overflow: hidden;
    background: var(--surface-0);
}
.jvm-container svg { background: transparent !important; }

/* ─────────────────────────────────────────────────────
   §13  PAGE HEADER
───────────────────────────────────────────────────── */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-faint);
}
.page-header__title {
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: -.025em;
}
.page-header__title em {
    font-style: normal;
    color: var(--cyan);
}
.page-header__sub {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-secondary);
    margin-top: 4px;
}
.page-header__right {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* ─────────────────────────────────────────────────────
   §14  TOAST NOTIFICATIONS
───────────────────────────────────────────────────── */
#toast-root {
    position: fixed;
    bottom: 24px; right: 24px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 8px;
    pointer-events: none;
}
.toast {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: var(--r-md);
    font-family: var(--font-mono);
    font-size: 12px;
    pointer-events: all;
    transform: translateX(110%);
    opacity: 0;
    transition: transform .35s var(--ease), opacity .35s var(--ease);
    max-width: 320px;
    backdrop-filter: blur(8px);
}
.toast.show { transform: none; opacity: 1; }
.toast--success {
    background: rgba(10,18,32,.9);
    border: 1px solid rgba(74,222,128,.3);
    color: var(--green);
    box-shadow: 0 0 20px rgba(74,222,128,.12);
}
.toast--error {
    background: rgba(10,18,32,.9);
    border: 1px solid rgba(248,113,113,.3);
    color: var(--red);
}
.toast--info {
    background: rgba(10,18,32,.9);
    border: 1px solid rgba(34,211,238,.25);
    color: var(--cyan);
}
.toast__icon { font-size: 14px; flex-shrink: 0; }

/* ─────────────────────────────────────────────────────
   §15  SCROLLBAR
───────────────────────────────────────────────────── */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border-subtle); border-radius: 99px; }
::-webkit-scrollbar-thumb:hover { background: var(--border-strong); }

/* ─────────────────────────────────────────────────────
   §16  COUNTRY FLAG CELL
───────────────────────────────────────────────────── */
.flag-cell { display: flex; align-items: center; gap: 8px; }

/* ─────────────────────────────────────────────────────
   §17  RESPONSIVE
───────────────────────────────────────────────────── */
@media (max-width: 1100px) {
    .g-3  { grid-template-columns: repeat(2,1fr); }
    .g-41 { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    body { padding: 14px; }
    .g-3, .g-2 { grid-template-columns: 1fr; }
    .page-header { flex-direction: column; align-items: flex-start; gap: 12px; }
}
.g-traffic-custom {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.g-traffic-right {
    display: grid;
    grid-template-rows: 1fr 1fr;
    gap: 16px;
}
.table-scroll {
    max-height: 260px; 
    overflow-y: auto;
}

/* responsive */
@media (max-width: 1100px) {
    .g-traffic-custom {
        grid-template-columns: 1fr;
    }
}

</style>

{{-- ═══════════════════════════════════════
     TOAST CONTAINER
═══════════════════════════════════════ --}}
<div id="toast-root" aria-live="polite"></div>

{{-- ═══════════════════════════════════════
     WRAPPER
═══════════════════════════════════════ --}}
<div class="scc-wrap">

{{-- ── PAGE HEADER ──────────────────────── --}}
<header class="page-header">
    <div>
        <h1 class="page-header__title">Security <em>Command Center</em></h1>
        <p class="page-header__sub" id="js-clock" aria-label="Current time">Initializing...</p>
    </div>
    <div class="page-header__right">
        <div class="live">
            <span class="live__dot" aria-hidden="true"></span>
            <span class="t-label" style="color:var(--green)">Live monitoring</span>
        </div>
    </div>
</header>

{{-- ── STAT CARDS ───────────────────────── --}}
<div class="g-3" role="region" aria-label="Traffic statistics">

    <div class="card stat stat--cyan" aria-label="Total visitors">
        <span class="stat__orb" aria-hidden="true"></span>
        <p class="t-label">Total Visitors</p>
        <p class="stat__value">{{ number_format($totalVisitors) }}</p>
        <p class="stat__sub">All traffic · last 24 h</p>
    </div>

    <div class="card stat stat--green" aria-label="Human visitors">
        <span class="stat__orb" aria-hidden="true"></span>
        <p class="t-label">Human Visitors</p>
        <p class="stat__value">{{ number_format($humanVisitors) }}</p>
        <p class="stat__sub">
            @if($totalVisitors > 0)
                {{ number_format($humanVisitors / $totalVisitors * 100, 1) }}% of total
            @else
                No data yet
            @endif
        </p>
    </div>

    <div class="card stat stat--red" aria-label="Bot visitors">
        <span class="stat__orb" aria-hidden="true"></span>
        <p class="t-label">Bot Visitors</p>
        <p class="stat__value">{{ number_format($botVisitors) }}</p>
        <p class="stat__sub">
            @if($totalVisitors > 0)
                {{ number_format($botVisitors / $totalVisitors * 100, 1) }}% of total
            @else
                No data yet
            @endif
        </p>
    </div>

</div>

{{-- ── SYSTEM GAUGES ─────────────────────── --}}
<div class="g-2" role="region" aria-label="System health">

    <div class="card gauge gauge--cyan">
        <div class="gauge__header">
            <div>
                <p class="t-label">CPU Usage</p>
                <p class="gauge__value" id="js-cpu-val">—</p>
            </div>
            <span class="gauge__status" id="js-cpu-status" style="color:var(--cyan)">Nominal</span>
        </div>
        <div class="track" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="js-cpu-track">
            <div class="track__fill" id="js-cpu-bar" style="width:0%"></div>
        </div>
        <div class="gauge__meta">
            <span>0%</span>
            <span id="js-cpu-desc">Fetching…</span>
            <span>100%</span>
        </div>
    </div>

    <div class="card gauge gauge--violet">
        <div class="gauge__header">
            <div>
                <p class="t-label">Memory Usage</p>
                <p class="gauge__value" id="js-ram-val">—</p>
            </div>
            <span class="gauge__status" id="js-ram-status" style="color:var(--violet)">Nominal</span>
        </div>
        <div class="track" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="js-ram-track">
            <div class="track__fill" id="js-ram-bar" style="width:0%"></div>
        </div>
        <div class="gauge__meta">
            <span>0%</span>
            <span id="js-ram-desc">Fetching…</span>
            <span>100%</span>
        </div>
    </div>

</div>

<div class="g-traffic-custom">

    {{-- LEFT: TRAFFIC --}}
    <div>
        <div class="g-1" role="region" aria-label="Traffic timeline">
            <div class="card">
                <div class="card__header">
                    <span class="t-section">Traffic Timeline</span>
                    <div style="display:flex;align-items:center;gap:14px;font-family:var(--font-mono);font-size:10px;color:var(--text-secondary)">
                        <span><span style="color:var(--green)">◆</span> Human</span>
                        <span><span style="color:var(--red)">◆</span> Bot</span>
                    </div>
                </div>
                <div class="card__body">
                    <div id="js-traffic-chart" style="height:260px"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: 2 TABLE --}}
    <div class="g-traffic-right">

        {{-- TOP IP --}}
        <div>
            <div class="card">
                <div class="card__header">
                    <span class="t-section">Top Attacker IPs</span>
                    <span class="t-label" style="color:var(--red)">High Risk</span>
                </div>
                <div class="card__body--flush table-scroll">
                    <table class="dtable" aria-label="Top attacker IP addresses">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>IP Address</th>
                                <th>Requests</th>
                            </tr>
                        </thead>
                        <tbody id="js-top-ips">
                            <tr><td class="empty-cell" colspan="3">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TOP COUNTRIES --}}
        <div>
            <div class="card">
                <div class="card__header">
                    <span class="t-section">Top Countries</span>
                    <span class="t-label">Geo Distribution</span>
                </div>
                <div class="card__body--flush table-scroll">
                    <table class="dtable" aria-label="Top countries by visitor count">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>Country</th>
                                <th>Visitors</th>
                            </tr>
                        </thead>
                        <tbody id="js-countries">
                            <tr><td class="empty-cell" colspan="3">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="g-41" role="region" aria-label="Attack map and latest visitors">

    {{-- LEFT: MAP --}}
    <div class="card">
        <div class="card__header">
            <span class="t-section">Live Attack Map</span>
            <div class="live">
                <span class="live__dot"></span>
                <span class="t-label" style="color:var(--green)">Real-time</span>
            </div>
        </div>
        <div class="card__body">
            <div id="attackMap" aria-label="World map showing attack origins"></div>
        </div>
    </div>

    {{-- RIGHT: LATEST VISITORS --}}
    <div class="card">
        <div class="card__header">
            <span class="t-section">Latest Visitors</span>
            <span class="t-label">{{ count($latestVisitors) }} entries</span>
        </div>
        <div class="card__body--flush">
            <table class="dtable" aria-label="Latest visitor records">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Type</th>
                        <th>Threat</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($latestVisitors as $v)
                <tr>
                    <td class="td-mono">{{ $v->ip }}</td>

                    <td>
                        @if($v->is_bot)
                            <span class="badge badge--bot">⚠ Bot</span>
                        @else
                            <span class="badge badge--human">✓ Human</span>
                        @endif
                    </td>

                    <td>
                        @php
                            $lvl = in_array($v->threat, ['CRITICAL','HIGH','MEDIUM','LOW']) ? $v->threat : 'LOW';
                        @endphp
                        <span class="threat threat--{{ $lvl }}">{{ $lvl }}</span>
                    </td>

                    <td>
                        <button class="btn-block"
                                data-ip="{{ $v->ip }}"
                                onclick="Actions.blockIp(this)">
                            ✕ Block
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td class="empty-cell" colspan="4">No visitor records</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
{{-- ── LIVE SECURITY EVENTS ──────────────── --}}
<div class="g-1" role="region" aria-label="Live security events">
    <div class="card">
        <div class="card__header">
            <span class="t-section">Live Security Events</span>
            <div class="live">
                <span class="live__dot" aria-hidden="true"></span>
                <span class="t-label" style="color:var(--green)">Streaming</span>
            </div>
        </div>
        <div class="card__body--flush">
            <table class="dtable" aria-label="Live security events">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Country</th>
                        <th>Threat</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody id="js-events">
                    <tr><td class="empty-cell" colspan="4">Waiting for events…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>{{-- /scc-wrap --}}

{{-- ═══════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

<script>
'use strict';

/* ══════════════════════════════════════════════════════════════
   MODULE 1 · CSRF TOKEN (retrieved once, shared everywhere)
══════════════════════════════════════════════════════════════ */
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ══════════════════════════════════════════════════════════════
   MODULE 2 · API SERVICE
   Centralised fetch wrapper: handles JSON parsing, errors,
   and injects CSRF token on write requests.
══════════════════════════════════════════════════════════════ */
const API = {
    async get(path) {
        const res = await fetch(path, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!res.ok) throw new Error(`API ${res.status}: ${path}`);
        return res.json();
    },

    async post(path, body = {}) {
        const res = await fetch(path, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });
        if (!res.ok) throw new Error(`API ${res.status}: ${path}`);
        return res.json();
    },
};

/* ══════════════════════════════════════════════════════════════
   MODULE 3 · SANITIZE  (XSS prevention)
   Never interpolate raw API data into innerHTML without this.
══════════════════════════════════════════════════════════════ */
const Sanitize = (() => {
    const el = document.createElement('div');
    return {
        html(str) {
            el.textContent = String(str ?? '');
            return el.innerHTML;
        },
        threat(raw) {
            const allowed = ['CRITICAL', 'HIGH', 'MEDIUM', 'LOW'];
            return allowed.includes(String(raw).toUpperCase())
                ? String(raw).toUpperCase()
                : 'LOW';
        },
    };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 4 · TOAST SYSTEM
══════════════════════════════════════════════════════════════ */
const Toast = (() => {
    const root = document.getElementById('toast-root');

    function show(message, type = 'info', duration = 3500) {
        const ICONS = { success: '✓', error: '✕', info: '◆' };
        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `<span class="toast__icon">${ICONS[type] ?? '◆'}</span><span>${Sanitize.html(message)}</span>`;
        root.appendChild(toast);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => toast.classList.add('show'));
        });

        setTimeout(() => {
            toast.classList.remove('show');
            toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        }, duration);
    }

    return { show };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 5 · CLOCK
══════════════════════════════════════════════════════════════ */
const Clock = (() => {
    const el = document.getElementById('js-clock');

    function tick() {
        el.textContent = new Date().toLocaleString('en-GB', {
            dateStyle: 'medium',
            timeStyle: 'medium',
        });
    }
    tick();
    return { init: () => setInterval(tick, 1000) };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 6 · TRAFFIC CHART (ApexCharts)
══════════════════════════════════════════════════════════════ */
const TrafficChart = (() => {
    const MAX_PTS = 20;
    const state = { human: [], bot: [], labels: [] };

    const options = {
        chart: {
            type: 'area',
            height: 260,
            background: 'transparent',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 500 },
            fontFamily: "'Space Mono', monospace",
        },
        series: [
            { name: 'Human', data: [] },
            { name: 'Bot',   data: [] },
        ],
        xaxis: {
            categories: [],
            labels: { style: { colors: '#334155', fontSize: '9px' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            min: 0,
            labels: { style: { colors: '#334155', fontSize: '9px' } },
        },
        colors: ['#4ade80', '#f87171'],
        fill: {
            type: 'gradient',
            gradient: { opacityFrom: 0.2, opacityTo: 0.01, type: 'vertical' },
        },
        stroke: { width: 1.5, curve: 'smooth' },
        grid: {
            borderColor: 'rgba(255,255,255,.04)',
            strokeDashArray: 3,
            xaxis: { lines: { show: false } },
        },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '10px', fontFamily: "'Space Mono', monospace" },
        },
        legend: { show: false },
        dataLabels: { enabled: false },
    };

    const instance = new ApexCharts(
        document.querySelector('#js-traffic-chart'),
        options
    );
    instance.render();

    async function refresh() {
        try {
            const data = await API.get('/api/traffic-stats');
            const ts = new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            state.human.push(data.human ?? 0);
            state.bot.push(data.bot ?? 0);
            state.labels.push(ts);

            if (state.human.length > MAX_PTS) {
                state.human.shift();
                state.bot.shift();
                state.labels.shift();
            }

            instance.updateOptions({
                series: [
                    { name: 'Human', data: [...state.human] },
                    { name: 'Bot',   data: [...state.bot] },
                ],
                xaxis: { categories: [...state.labels] },
            }, false, false);
        } catch (err) {
            console.warn('[TrafficChart]', err);
        }
    }

    return { init: () => { refresh(); return setInterval(refresh, 5000); } };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 7 · TOP IP TABLE
══════════════════════════════════════════════════════════════ */
const TopIpTable = (() => {
    const tbody = document.getElementById('js-top-ips');

    function render(rows) {
        if (!rows.length) {
            tbody.innerHTML = `<tr><td class="empty-cell" colspan="3">No data available</td></tr>`;
            return;
        }
        tbody.innerHTML = rows.map((row, i) => `
            <tr>
                <td class="td-mono t-dim">${i + 1}</td>
                <td class="td-mono">${Sanitize.html(row.ip)}</td>
                <td class="td-mono" style="color:var(--red);font-weight:700">
                    ${Number(row.total).toLocaleString()}
                </td>
            </tr>
        `).join('');
    }

    async function refresh() {
        try {
            render(await API.get('/api/top-ip'));
        } catch (err) {
            console.warn('[TopIpTable]', err);
        }
    }

    return { init: () => { refresh(); return setInterval(refresh, 10_000); } };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 8 · COUNTRY TABLE
══════════════════════════════════════════════════════════════ */
const CountryTable = (() => {
    const tbody = document.getElementById('js-countries');

    function render(rows) {
        if (!rows.length) {
            tbody.innerHTML = `<tr><td class="empty-cell" colspan="3">No data available</td></tr>`;
            return;
        }
        tbody.innerHTML = rows.map((row, i) => `
            <tr>
                <td class="td-mono t-dim">${i + 1}</td>
                <td>
                    <span class="flag-cell">
                        <span aria-hidden="true">🌍</span>
                        <span style="font-size:13px">${Sanitize.html(row.country)}</span>
                    </span>
                </td>
                <td class="td-mono" style="font-weight:700">
                    ${Number(row.total).toLocaleString()}
                </td>
            </tr>
        `).join('');
    }

    async function refresh() {
        try {
            render(await API.get('/api/country-stats'));
        } catch (err) {
            console.warn('[CountryTable]', err);
        }
    }

    return { init: () => { refresh(); return setInterval(refresh, 10_000); } };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 9 · ATTACK MAP (jsVectorMap)
══════════════════════════════════════════════════════════════ */
const AttackMap = (() => {
    const COUNTRY_CODES = {
        'Vietnam':        'VN',
        'United States':  'US',
        'China':          'CN',
        'Singapore':      'SG',
        'Russia':         'RU',
        'Germany':        'DE',
        'France':         'FR',
        'Japan':          'JP',
        'South Korea':    'KR',
        'India':          'IN',
        'Brazil':         'BR',
        'United Kingdom': 'GB',
        'Netherlands':    'NL',
        'Canada':         'CA',
        'Australia':      'AU',
    };

    const map = new jsVectorMap({
        selector: '#attackMap',
        map: 'world',
        zoomButtons: true,
        backgroundColor: 'transparent',
        regionStyle: {
            initial:  { fill: '#0d1f38', stroke: 'rgba(34,211,238,.08)', strokeWidth: 0.4 },
            hover:    { fill: '#1a3050', cursor: 'default' },
            selected: { fill: '#22d3ee' },
        },
        series: {
            regions: [{
                attribute: 'fill',
                scale: { min: '#152540', max: '#f87171' },
                normalizeFunction: 'polynomial',
                values: {},
            }],
        },
    });

    async function refresh() {
        try {
            const data = await API.get('/api/attack-map');
            const regions = {};
            data.forEach(row => {
                const code = COUNTRY_CODES[row.country];
                if (code) regions[code] = Number(row.total);
            });
            map.updateSeries([{ attribute: 'fill', values: regions }]);
        } catch (err) {
            console.warn('[AttackMap]', err);
        }
    }

    return { init: () => { refresh(); return setInterval(refresh, 10_000); } };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 10 · SECURITY EVENTS TABLE
══════════════════════════════════════════════════════════════ */
const SecurityEvents = (() => {
    const tbody = document.getElementById('js-events');

    function render(rows) {
        if (!rows.length) {
            tbody.innerHTML = `<tr><td class="empty-cell" colspan="4">No security events</td></tr>`;
            return;
        }
        tbody.innerHTML = rows.map(row => {
            const threat = Sanitize.threat(row.threat);
            return `
                <tr>
                    <td class="td-mono">${Sanitize.html(row.ip)}</td>
                    <td class="t-dim" style="font-size:13px">${Sanitize.html(row.country)}</td>
                    <td><span class="threat threat--${threat}">${threat}</span></td>
                    <td class="td-mono t-dim" style="font-size:10px">${Sanitize.html(row.created_at)}</td>
                </tr>
            `;
        }).join('');
    }

    async function refresh() {
        try {
            render(await API.get('/api/security-events'));
        } catch (err) {
            console.warn('[SecurityEvents]', err);
        }
    }

    return { init: () => { refresh(); return setInterval(refresh, 5000); } };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 11 · SYSTEM GAUGES  (CPU + RAM)
══════════════════════════════════════════════════════════════ */
const SystemGauges = (() => {

    function updateGauge({ valEl, barEl, statusEl, descEl, trackEl }, pct) {
        const clamped = Math.min(100, Math.max(0, Math.round(pct)));

        valEl.textContent    = `${clamped}%`;
        barEl.style.width    = `${clamped}%`;
        trackEl.setAttribute('aria-valuenow', clamped);

        if (clamped >= 90) {
            statusEl.textContent = 'Critical';
            statusEl.style.color = 'var(--red)';
            descEl.textContent   = 'Critical load';
        } else if (clamped >= 75) {
            statusEl.textContent = 'Warning';
            statusEl.style.color = 'var(--amber)';
            descEl.textContent   = 'Elevated load';
        } else {
            statusEl.textContent = 'Nominal';
            descEl.textContent   = 'Normal load';
        }
    }

    const CPU = {
        valEl:    document.getElementById('js-cpu-val'),
        barEl:    document.getElementById('js-cpu-bar'),
        statusEl: document.getElementById('js-cpu-status'),
        descEl:   document.getElementById('js-cpu-desc'),
        trackEl:  document.getElementById('js-cpu-track'),
    };
    const RAM = {
        valEl:    document.getElementById('js-ram-val'),
        barEl:    document.getElementById('js-ram-bar'),
        statusEl: document.getElementById('js-ram-status'),
        descEl:   document.getElementById('js-ram-desc'),
        trackEl:  document.getElementById('js-ram-track'),
    };

    async function refresh() {
        try {
            const data = await API.get('/api/system-stats');
            updateGauge(CPU, data.cpu ?? 0);
            updateGauge(RAM, data.ram ?? 0);
        } catch (err) {
            console.warn('[SystemGauges]', err);
        }
    }

    return { init: () => { refresh(); return setInterval(refresh, 3000); } };
})();

/* ══════════════════════════════════════════════════════════════
   MODULE 12 · ACTIONS  (block IP)
══════════════════════════════════════════════════════════════ */
const Actions = {
    async blockIp(btn) {
        const ip = btn.dataset.ip;
        if (!ip) return;

        const confirmed = confirm(`Block ${ip}?\n\nAll requests from this IP will be denied.`);
        if (!confirmed) return;

        btn.disabled = true;
        btn.textContent = '…';

        try {
            const data = await API.post('/api/firewall/block', { ip });
            Toast.show(data.message ?? `${ip} has been blocked.`, 'success');
            btn.closest('tr')?.remove();
        } catch (err) {
            console.error('[BlockIP]', err);
            Toast.show(`Failed to block ${ip}. Please try again.`, 'error');
            btn.disabled = false;
            btn.textContent = '✕ Block';
        }
    },
};

/* ══════════════════════════════════════════════════════════════
   BOOT — initialise all modules
══════════════════════════════════════════════════════════════ */
(function boot() {
    Clock.init();
    TrafficChart.init();
    TopIpTable.init();
    CountryTable.init();
    AttackMap.init();
    SecurityEvents.init();
    SystemGauges.init();
})();
</script>

@endsection