@extends('layouts.panel')

@section('content')

@php
    use Carbon\Carbon;

    $isPaginated = $events instanceof \Illuminate\Pagination\AbstractPaginator;
    $items       = $isPaginated ? $events->getCollection() : $events;
    $total       = $isPaginated ? $events->total() : $events->count();

    // Đếm theo severity nếu có field 'severity', ngược lại dùng 'type'
    $critCount = $items->filter(fn($e) =>
        strtoupper($e->severity ?? $e->type ?? '') === 'CRITICAL'
    )->count();

    $highCount = $items->filter(fn($e) =>
        strtoupper($e->severity ?? $e->type ?? '') === 'HIGH'
    )->count();

    // Map type → severity class
    $severityMap = [
        'CRITICAL'        => 'CRITICAL',
        'HIGH'            => 'HIGH',
        'MEDIUM'          => 'MEDIUM',
        'LOW'             => 'LOW',
        'SQL_INJECTION'   => 'CRITICAL',
        'XSS'             => 'HIGH',
        'BRUTE_FORCE'     => 'HIGH',
        'DDOS'            => 'CRITICAL',
        'SCAN'            => 'MEDIUM',
        'MALWARE'         => 'CRITICAL',
        'SUSPICIOUS'      => 'MEDIUM',
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════
   TOKENS
═══════════════════════════════════════════ */
:root {
    --bg:       #040c18;
    --surface:  #071525;
    --card:     #0a1e35;
    --card-2:   #0d2440;

    --border:   rgba(96,165,250,0.10);
    --border-2: rgba(96,165,250,0.22);

    --blue:   #60a5fa;
    --cyan:   #22d3ee;
    --green:  #4ade80;
    --red:    #f87171;
    --amber:  #fbbf24;
    --purple: #c084fc;

    --blue-a:   rgba(96,165,250,0.12);
    --cyan-a:   rgba(34,211,238,0.10);
    --green-a:  rgba(74,222,128,0.12);
    --red-a:    rgba(248,113,113,0.12);
    --amber-a:  rgba(251,191,36,0.10);
    --purple-a: rgba(192,132,252,0.12);

    --text:   #cbd5e1;
    --text-2: #64748b;
    --text-3: #1e3a5f;

    --f-ui:   'Outfit', sans-serif;
    --f-mono: 'IBM Plex Mono', monospace;
    --ease:   cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
button, input, select { font-family:var(--f-ui); }

body {
    font-family:var(--f-ui);
    background:var(--bg);
    color:var(--text);
    font-size:14px;
    -webkit-font-smoothing:antialiased;
}

::-webkit-scrollbar { width:4px; height:4px; }
::-webkit-scrollbar-thumb { background:var(--border-2); border-radius:99px; }

/* ═══════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════ */
.page-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:14px;
    margin-bottom:24px;
}

.page-title {
    font-size:1.5rem;
    font-weight:800;
    color:#fff;
    letter-spacing:-.03em;
}

.page-title em { font-style:normal; color:var(--red); }

.page-sub {
    margin-top:4px;
    font-size:12px;
    color:var(--text-2);
    font-family:var(--f-mono);
    display:flex;
    align-items:center;
    gap:8px;
}

.page-sub span { color:var(--text-3); }

/* ═══════════════════════════════════════════
   LIVE DOT
═══════════════════════════════════════════ */
.dot {
    display:inline-block;
    width:7px; height:7px;
    border-radius:50%;
    background:var(--red);
    box-shadow:0 0 8px var(--red);
    animation:beat 2s ease-in-out infinite;
    flex-shrink:0;
}

@keyframes beat {
    0%,100% { transform:scale(.85); opacity:.7; }
    50%      { transform:scale(1.2); opacity:1; }
}

/* ═══════════════════════════════════════════
   STATS CHIPS
═══════════════════════════════════════════ */
.stats-row {
    display:flex;
    align-items:center;
    gap:6px;
    flex-wrap:wrap;
}

.chip {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:99px;
    font-size:10px;
    font-weight:700;
    font-family:var(--f-mono);
    border:1px solid transparent;
}

.chip-total    { background:var(--blue-a);   color:var(--blue);   border-color:rgba(96,165,250,.2); }
.chip-critical { background:var(--red-a);    color:var(--red);    border-color:rgba(248,113,113,.2); }
.chip-high     { background:var(--amber-a);  color:var(--amber);  border-color:rgba(251,191,36,.2); }

/* ═══════════════════════════════════════════
   CARD
═══════════════════════════════════════════ */
.card {
    background:var(--card);
    border:1px solid var(--border);
    border-radius:18px;
    overflow:hidden;
    transition:border-color .22s var(--ease);
}

.card:hover { border-color:var(--border-2); }

.card-head {
    padding:14px 20px;
    border-bottom:1px solid var(--border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
}

.card-title {
    display:flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    font-weight:700;
    color:#fff;
}

.c-icon {
    width:26px; height:26px;
    border-radius:6px;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; flex-shrink:0;
}

.card-meta {
    font-size:10px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.08em;
    font-family:var(--f-mono);
}

/* ═══════════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════════ */
.filter-bar {
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}

.f-input,
.f-select {
    height:34px;
    background:rgba(255,255,255,0.04);
    border:1px solid var(--border-2);
    border-radius:8px;
    color:var(--text);
    padding:0 12px;
    font-size:12px;
    font-family:var(--f-mono);
    transition:all .18s var(--ease);
    outline:none;
}

.f-input { min-width:180px; }
.f-input::placeholder { color:var(--text-3); }
.f-select { cursor:pointer; }
.f-select option { background:#0a1e35; color:var(--text); }

.f-input:focus,
.f-select:focus {
    border-color:var(--red);
    background:rgba(248,113,113,0.05);
    box-shadow:0 0 0 3px rgba(248,113,113,0.07);
}

.btn-filter {
    height:34px; padding:0 14px;
    border-radius:8px;
    background:var(--red-a);
    color:var(--red);
    border:1px solid rgba(248,113,113,.22);
    font-size:12px; font-weight:700; font-family:var(--f-mono);
    cursor:pointer;
    display:inline-flex; align-items:center; gap:6px;
    transition:all .18s var(--ease);
}

.btn-filter:hover { background:rgba(248,113,113,.2); border-color:var(--red); }

.btn-clear {
    height:34px; padding:0 12px;
    border-radius:8px;
    background:transparent;
    color:var(--text-2);
    border:1px solid var(--border);
    font-size:12px; font-weight:600; font-family:var(--f-mono);
    cursor:pointer;
    text-decoration:none;
    display:inline-flex; align-items:center;
    transition:all .18s var(--ease);
}

.btn-clear:hover { color:var(--text); border-color:var(--border-2); }

/* ═══════════════════════════════════════════
   TABLE
═══════════════════════════════════════════ */
.dt-wrap { overflow-x:auto; }

.dt { width:100%; border-collapse:collapse; min-width:700px; }

.dt thead th {
    font-size:10px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:var(--text-3);
    padding:10px 16px;
    text-align:left;
    border-bottom:1px solid var(--border);
    white-space:nowrap;
}

.dt tbody tr { border-bottom:1px solid var(--border); transition:background .13s var(--ease); }
.dt tbody tr:last-child { border-bottom:none; }
.dt tbody tr:hover { background:rgba(248,113,113,0.03); }

.dt tbody td {
    padding:13px 16px;
    font-size:13px;
    color:var(--text);
    vertical-align:middle;
}

.mono  { font-family:var(--f-mono); font-size:12px; }
.dim   { color:var(--text-2); }
.clamp { max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }

/* IP cell */
.ip-cell { display:flex; align-items:center; gap:8px; }
.ip-av {
    width:30px; height:30px;
    border-radius:7px;
    display:flex; align-items:center; justify-content:center;
    font-size:11px; font-weight:700; font-family:var(--f-mono);
    flex-shrink:0;
    background:var(--red-a); color:var(--red);
}

/* ═══════════════════════════════════════════
   SEVERITY BADGES
═══════════════════════════════════════════ */
.sev {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 9px; border-radius:5px;
    font-size:10px; font-weight:700; font-family:var(--f-mono);
    letter-spacing:.05em; border:1px solid transparent; white-space:nowrap;
}

.sev-CRITICAL { background:var(--red);    color:#fff; box-shadow:0 0 10px rgba(248,113,113,.4); border-color:var(--red); }
.sev-HIGH     { background:var(--amber-a);color:var(--amber); border-color:rgba(251,191,36,.35); }
.sev-MEDIUM   { background:var(--purple-a);color:var(--purple); border-color:rgba(192,132,252,.3); }
.sev-LOW      { background:var(--green-a); color:var(--green);  border-color:rgba(74,222,128,.3); }

/* ═══════════════════════════════════════════
   TYPE LABEL
═══════════════════════════════════════════ */
.type-label {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 9px; border-radius:5px;
    font-size:10px; font-weight:700; font-family:var(--f-mono);
    background:var(--blue-a); color:var(--blue);
    border:1px solid rgba(96,165,250,.2);
    white-space:nowrap; letter-spacing:.04em;
}

/* NEW FLASH ROW */
@keyframes flash-in {
    from { background:rgba(248,113,113,0.12); }
    to   { background:transparent; }
}

.dt tbody tr.new-event { animation:flash-in 2s var(--ease) forwards; }

/* ═══════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════ */
.pager {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 20px; border-top:1px solid var(--border);
    flex-wrap:wrap; gap:10px;
}

.pager-info { font-size:11px; color:var(--text-2); font-family:var(--f-mono); }

.pager-links { display:flex; align-items:center; gap:4px; }

.pager-links a,
.pager-links span {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:30px; height:30px; padding:0 8px;
    border-radius:6px; font-size:11px; font-family:var(--f-mono);
    font-weight:600; border:1px solid var(--border); color:var(--text-2);
    text-decoration:none; transition:all .15s var(--ease);
}

.pager-links a:hover  { background:var(--red-a); color:var(--red); border-color:rgba(248,113,113,.2); }
.pager-links span.cur { background:var(--red-a); color:var(--red); border-color:rgba(248,113,113,.3); }
.pager-links span.off { opacity:.3; pointer-events:none; }

/* ═══════════════════════════════════════════
   EMPTY STATE
═══════════════════════════════════════════ */
.empty {
    padding:56px 20px; text-align:center;
    display:flex; flex-direction:column; align-items:center; gap:10px;
}

.empty-icon  { font-size:36px; margin-bottom:4px; }
.empty-title { font-size:14px; font-weight:600; color:var(--text); }
.empty-sub   { font-size:12px; color:var(--text-2); font-family:var(--f-mono); }

/* ═══════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════ */
@media (max-width:768px) {
    .page-header { flex-direction:column; align-items:flex-start; }
    .f-input { min-width:0; flex:1; }
}
</style>

{{-- ════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════ --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Security <em>Events</em></h1>
        <div class="page-sub">
            <span>ShieldOps</span><span>›</span>Security<span>›</span>Events
        </div>
    </div>

    <div class="stats-row">
        <span class="chip chip-total">
            <svg width="7" height="7" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
            {{ number_format($total) }} events
        </span>
        @if($critCount > 0)
        <span class="chip chip-critical">⚡ {{ $critCount }} critical</span>
        @endif
        @if($highCount > 0)
        <span class="chip chip-high">▲ {{ $highCount }} high</span>
        @endif
    </div>
</div>

{{-- ════════════════════════════════════════
     CARD
════════════════════════════════════════ --}}
<div class="card">

    {{-- Head --}}
    <div class="card-head">
        <div class="card-title">
            <div class="c-icon" style="background:var(--red-a);color:var(--red)">⚡</div>
            Live Security Events
            <div style="display:flex;align-items:center;gap:5px;margin-left:4px">
                <span class="dot"></span>
                <span class="card-meta" style="color:var(--green)">LIVE</span>
            </div>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ request()->url() }}" class="filter-bar">
            <input
                type="text"
                name="search"
                class="f-input"
                placeholder="Search IP or description..."
                value="{{ request('search') }}"
            >

            <select name="type" class="f-select">
                <option value="">All Types</option>
                <option value="SQL_INJECTION" @selected(request('type') === 'SQL_INJECTION')>SQL Injection</option>
                <option value="XSS"           @selected(request('type') === 'XSS')>XSS</option>
                <option value="BRUTE_FORCE"   @selected(request('type') === 'BRUTE_FORCE')>Brute Force</option>
                <option value="DDOS"          @selected(request('type') === 'DDOS')>DDoS</option>
                <option value="SCAN"          @selected(request('type') === 'SCAN')>Port Scan</option>
                <option value="MALWARE"       @selected(request('type') === 'MALWARE')>Malware</option>
                <option value="SUSPICIOUS"    @selected(request('type') === 'SUSPICIOUS')>Suspicious</option>
            </select>

            <select name="severity" class="f-select">
                <option value="">All Severity</option>
                <option value="CRITICAL" @selected(request('severity') === 'CRITICAL')>Critical</option>
                <option value="HIGH"     @selected(request('severity') === 'HIGH')>High</option>
                <option value="MEDIUM"   @selected(request('severity') === 'MEDIUM')>Medium</option>
                <option value="LOW"      @selected(request('severity') === 'LOW')>Low</option>
            </select>

            <button type="submit" class="btn-filter">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter
            </button>

            @if(request()->hasAny(['search', 'type', 'severity']))
                <a href="{{ request()->url() }}" class="btn-clear">✕ Clear</a>
            @endif
        </form>
    </div>

    {{-- Body --}}
    @if($items->isEmpty())
        <div class="empty">
            <div class="empty-icon">🛡️</div>
            <div class="empty-title">No security events found</div>
            <div class="empty-sub">System is clean — no threats detected matching your filter</div>
        </div>
    @else
        <div class="dt-wrap">
            <table class="dt">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Event Type</th>
                        <th>Severity</th>
                        <th>Description</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($items as $event)
                @php
                    $typeRaw  = strtoupper($event->type ?? 'UNKNOWN');
                    $sevRaw   = strtoupper($event->severity ?? $severityMap[$typeRaw] ?? 'LOW');
                    $sevClass = in_array($sevRaw, ['CRITICAL','HIGH','MEDIUM','LOW']) ? $sevRaw : 'LOW';
                    $initials = strtoupper(substr($event->ip ?? '?', 0, 2));
                    $ts       = $event->created_at instanceof \Carbon\Carbon
                                ? $event->created_at
                                : \Carbon\Carbon::parse($event->created_at);
                @endphp
                <tr>
                    {{-- IP --}}
                    <td>
                        <div class="ip-cell">
                            <div class="ip-av">{{ $initials }}</div>
                            <span class="mono">{{ $event->ip }}</span>
                        </div>
                    </td>

                    {{-- Type --}}
                    <td>
                        <span class="type-label">{{ $typeRaw }}</span>
                    </td>

                    {{-- Severity --}}
                    <td>
                        <span class="sev sev-{{ $sevClass }}">
                            @if($sevClass === 'CRITICAL')
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                            @elseif($sevClass === 'HIGH')
                                ▲
                            @elseif($sevClass === 'MEDIUM')
                                ◆
                            @else
                                ●
                            @endif
                            {{ $sevClass }}
                        </span>
                    </td>

                    {{-- Description --}}
                    <td>
                        <span
                            class="dim clamp"
                            title="{{ $event->description ?? '' }}"
                        >{{ $event->description ?? '—' }}</span>
                    </td>

                    {{-- Timestamp --}}
                    <td>
                        <span class="mono dim" style="font-size:11px;white-space:nowrap">
                            {{ $ts->format('d M Y') }}
                        </span>
                        <div class="mono" style="font-size:10px;color:var(--text-3);margin-top:1px">
                            {{ $ts->format('H:i:s') }}
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($isPaginated && $events->hasPages())
        <div class="pager">
            <div class="pager-info">
                Showing {{ $events->firstItem() }}–{{ $events->lastItem() }}
                of {{ number_format($events->total()) }} events
            </div>
            <div class="pager-links">

                @if($events->onFirstPage())
                    <span class="off">‹</span>
                @else
                    <a href="{{ $events->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}">‹</a>
                @endif

                @foreach($events->getUrlRange(max(1, $events->currentPage()-2), min($events->lastPage(), $events->currentPage()+2)) as $page => $url)
                    @if($page == $events->currentPage())
                        <span class="cur">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($events->hasMorePages())
                    <a href="{{ $events->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}">›</a>
                @else
                    <span class="off">›</span>
                @endif

            </div>
        </div>
        @else
        <div class="pager">
            <div class="pager-info">Showing {{ $items->count() }} events</div>
        </div>
        @endif

    @endif

</div>

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
/* ─────────────────────────────────────────────────────
    TABLE SYNC (Security Red Theme)
───────────────────────────────────────────────────── */
.dtable {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: rgba(10, 18, 32, 0.5);
}

/* HEADER - Nhấn mạnh tông đỏ cảnh báo */
.dtable thead th {
    font-family: var(--font-mono) !important;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #CCFFFF; /* Đổi sang đỏ để đồng bộ security */
    padding: 16px 15px;
    background: rgba(15, 23, 42, 0.9);
    border-bottom: 2px solid rgba(248, 113, 113, 0.2); 
    border-right: 1px solid var(--border-faint);
    text-align: left;
    text-shadow: 0 0 8px rgba(248, 113, 113, 0.3);
}

/* ROW */
.dtable tbody tr {
    transition: all 0.2s ease;
}

.dtable tbody tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.015);
}

/* HOVER - Hiệu ứng kính mờ đỏ nhẹ */
.dtable tbody tr:hover {
    background: rgba(248, 113, 113, 0.04) !important;
    backdrop-filter: blur(4px);
}

/* CELL */
.dtable td {
    padding: 14px 15px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    border-right: 1px solid var(--border-faint);
    border-bottom: 1px solid var(--border-faint);
}

/* Bỏ viền dòng cuối và cột cuối */
.dtable tbody tr:last-child td {
    border-bottom: none;
}

.dtable td:last-child,
.dtable th:last-child {
    border-right: none;
}

/* TYPE BADGE - Làm nổi bật loại sự kiện */
.badge-danger {
    background: rgba(248, 113, 113, 0.1);
    color: var(--red);
    padding: 4px 10px;
    border-radius: 6px;
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    border: 1px solid rgba(248, 113, 113, 0.2);
    display: inline-block;
    text-shadow: 0 0 5px var(--red);
}

/* Bo góc cho bảng */
.dtable thead th:first-child { border-top-left-radius: 14px; }
.dtable thead th:last-child { border-top-right-radius: 14px; }

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