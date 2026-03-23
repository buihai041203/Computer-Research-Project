@extends('layouts.panel')

@section('content')

{{-- ─────────────────────────────────────────────────────────────
     NOTE: View tự nhận biết $logs là Collection hay Paginator.
     Controller có thể trả về:
       return view('logs.index', ['logs' => TrafficLog::orderByDesc('created_at')->get()]);
     hoặc:
       return view('logs.index', ['logs' => TrafficLog::orderByDesc('created_at')->paginate(50)]);
     Đều chạy được.
───────────────────────────────────────────────────────────────── --}}

@php
    use Illuminate\Pagination\AbstractPaginator;

    $isPaginated = $logs instanceof AbstractPaginator;

    // Lấy items thực tế để đếm trên trang hiện tại
    $items = $isPaginated ? $logs->getCollection() : $logs;

    $totalCount = $isPaginated ? $logs->total() : $logs->count();
    $humanCount = $items->where('type', 'human')->count();
    $botCount   = $items->where('type', 'bot')->count();
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

    --blue-a:   rgba(96,165,250,0.12);
    --cyan-a:   rgba(34,211,238,0.10);
    --green-a:  rgba(74,222,128,0.12);
    --red-a:    rgba(248,113,113,0.12);
    --amber-a:  rgba(251,191,36,0.10);

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

.page-title em { font-style:normal; color:var(--cyan); }

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

.chip-total { background:var(--blue-a);  color:var(--blue);  border-color:rgba(96,165,250,.2); }
.chip-human { background:var(--green-a); color:var(--green); border-color:rgba(74,222,128,.2); }
.chip-bot   { background:var(--red-a);   color:var(--red);   border-color:rgba(248,113,113,.2); }

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

.f-input { min-width:190px; }
.f-select { cursor:pointer; }
.f-input::placeholder { color:var(--text-3); }

.f-input:focus,
.f-select:focus {
    border-color:var(--cyan);
    background:rgba(34,211,238,0.05);
    box-shadow:0 0 0 3px rgba(34,211,238,0.07);
}

.f-select option { background:#0a1e35; color:var(--text); }

.btn-filter {
    height:34px;
    padding:0 14px;
    border-radius:8px;
    background:var(--blue-a);
    color:var(--blue);
    border:1px solid rgba(96,165,250,.2);
    font-size:12px;
    font-weight:700;
    font-family:var(--f-mono);
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:6px;
    transition:all .18s var(--ease);
}

.btn-filter:hover { background:rgba(96,165,250,.2); border-color:var(--blue); }

.btn-clear {
    height:34px;
    padding:0 12px;
    border-radius:8px;
    background:transparent;
    color:var(--text-2);
    border:1px solid var(--border);
    font-size:12px;
    font-weight:600;
    font-family:var(--f-mono);
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    transition:all .18s var(--ease);
}

.btn-clear:hover { color:var(--text); border-color:var(--border-2); }

/* ═══════════════════════════════════════════
   TABLE
═══════════════════════════════════════════ */
.dt-wrap { overflow-x:auto; }

.dt { width:100%; border-collapse:collapse; min-width:780px; }

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
.dt tbody tr:hover { background:rgba(96,165,250,0.04); }

.dt tbody td {
    padding:12px 16px;
    font-size:13px;
    color:var(--text);
    vertical-align:middle;
}

.mono    { font-family:var(--f-mono); font-size:11.5px; }
.dim     { color:var(--text-2); }
.clamp   { max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }

/* IP cell */
.ip-cell { display:flex; align-items:center; gap:8px; }
.ip-av   { width:28px; height:28px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; font-family:var(--f-mono); flex-shrink:0; }

/* ═══════════════════════════════════════════
   BADGES
═══════════════════════════════════════════ */
.badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 8px; border-radius:5px;
    font-size:10px; font-weight:700; font-family:var(--f-mono);
    letter-spacing:.04em; white-space:nowrap; border:1px solid transparent;
}

.b-bot   { background:var(--red-a);   color:var(--red);   border-color:rgba(248,113,113,.2); }
.b-human { background:var(--green-a); color:var(--green); border-color:rgba(74,222,128,.2); }

.threat {
    display:inline-block; padding:2px 7px; border-radius:5px;
    font-size:10px; font-weight:700; font-family:var(--f-mono);
    letter-spacing:.05em; border:1px solid transparent; white-space:nowrap;
}

.t-CRITICAL { background:var(--red);    color:#fff; box-shadow:0 0 8px rgba(248,113,113,.35); border-color:var(--red); }
.t-HIGH     { background:var(--red-a);  color:var(--red);   border-color:rgba(248,113,113,.3); }
.t-MEDIUM   { background:var(--amber-a);color:var(--amber); border-color:rgba(251,191,36,.3); }
.t-LOW      { background:var(--green-a);color:var(--green); border-color:rgba(74,222,128,.3); }

/* ═══════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════ */
.pager {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px 20px;
    border-top:1px solid var(--border);
    flex-wrap:wrap;
    gap:10px;
}

.pager-info { font-size:11px; color:var(--text-2); font-family:var(--f-mono); }

.pager-links { display:flex; align-items:center; gap:4px; }

.pager-links a,
.pager-links span {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:30px; height:30px; padding:0 8px;
    border-radius:6px;
    font-size:11px; font-family:var(--f-mono); font-weight:600;
    border:1px solid var(--border); color:var(--text-2);
    text-decoration:none; transition:all .15s var(--ease);
}

.pager-links a:hover      { background:var(--blue-a); color:var(--blue); border-color:rgba(96,165,250,.2); }
.pager-links span.cur     { background:var(--blue-a); color:var(--blue); border-color:rgba(96,165,250,.3); }
.pager-links span.off     { opacity:.3; pointer-events:none; }

/* ═══════════════════════════════════════════
   EMPTY
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
        <h1 class="page-title">System <em>Logs</em></h1>
        <div class="page-sub">
            <span>ShieldOps</span><span>›</span>Logs<span>›</span>All Records
        </div>
    </div>

    <div class="stats-row">
        <span class="chip chip-total">
            <svg width="7" height="7" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
            {{ number_format($totalCount) }} records
        </span>
        <span class="chip chip-human">↑ {{ $humanCount }} human</span>
        <span class="chip chip-bot">⚠ {{ $botCount }} bot</span>
    </div>
</div>

{{-- ════════════════════════════════════════
     CARD
════════════════════════════════════════ --}}
<div class="card">

    {{-- Head + Filter --}}
    <div class="card-head">
        <div class="card-title">
            <div class="c-icon" style="background:var(--cyan-a);color:var(--cyan)">📋</div>
            Access Log Records
        </div>

        <form method="GET" action="{{ request()->url() }}" class="filter-bar">
            <input
                type="text"
                name="search"
                class="f-input"
                placeholder="Search IP, country, agent..."
                value="{{ request('search') }}"
            >

            <select name="type" class="f-select">
                <option value="">All Types</option>
                <option value="human" @selected(request('type') === 'human')>Human</option>
                <option value="bot"   @selected(request('type') === 'bot')>Bot</option>
            </select>

            <select name="threat" class="f-select">
                <option value="">All Threats</option>
                <option value="CRITICAL" @selected(request('threat') === 'CRITICAL')>Critical</option>
                <option value="HIGH"     @selected(request('threat') === 'HIGH')>High</option>
                <option value="MEDIUM"   @selected(request('threat') === 'MEDIUM')>Medium</option>
                <option value="LOW"      @selected(request('threat') === 'LOW')>Low</option>
            </select>

            <button type="submit" class="btn-filter">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter
            </button>

            @if(request()->hasAny(['search', 'type', 'threat']))
                <a href="{{ request()->url() }}" class="btn-clear">✕ Clear</a>
            @endif
        </form>
    </div>

    {{-- Body --}}
    @if($items->isEmpty())
        <div class="empty">
            <div class="empty-icon">📭</div>
            <div class="empty-title">No log records found</div>
            <div class="empty-sub">Try adjusting your filters or check back later</div>
        </div>
    @else
        <div class="dt-wrap">
            <table class="dt">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Country</th>
                        <th>Type</th>
                        <th>Threat</th>
                        <th>User Agent</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($items as $log)
                @php
                    $isBot    = ($log->type ?? '') === 'bot';
                    $threat   = in_array($log->threat ?? '', ['CRITICAL','HIGH','MEDIUM','LOW'])
                                ? $log->threat : 'LOW';
                    $initials = strtoupper(substr($log->ip ?? '?', 0, 2));
                    $avBg     = $isBot ? 'var(--red-a)'   : 'var(--green-a)';
                    $avCl     = $isBot ? 'var(--red)'     : 'var(--green)';
                    $ts       = $log->created_at instanceof \Carbon\Carbon
                                ? $log->created_at
                                : \Carbon\Carbon::parse($log->created_at);
                @endphp
                <tr>
                    {{-- IP --}}
                    <td>
                        <div class="ip-cell">
                            <div class="ip-av" style="background:{{ $avBg }};color:{{ $avCl }}">
                                {{ $initials }}
                            </div>
                            <span class="mono">{{ $log->ip }}</span>
                        </div>
                    </td>

                    {{-- Country --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:7px">
                            <span>🌍</span>
                            <span class="dim">{{ $log->country ?? '—' }}</span>
                        </div>
                    </td>

                    {{-- Type --}}
                    <td>
                        <span class="badge {{ $isBot ? 'b-bot' : 'b-human' }}">
                            <svg width="6" height="6" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                            {{ $isBot ? 'Bot' : 'Human' }}
                        </span>
                    </td>

                    {{-- Threat --}}
                    <td>
                        <span class="threat t-{{ $threat }}">{{ $threat }}</span>
                    </td>

                    {{-- User Agent --}}
                    <td>
                        <span class="mono dim clamp" title="{{ $log->user_agent ?? '' }}">
                            {{ $log->user_agent ?? '—' }}
                        </span>
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

        {{-- Pagination — chỉ hiển thị nếu là Paginator --}}
        @if($isPaginated && $logs->hasPages())
        <div class="pager">
            <div class="pager-info">
                Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} records
            </div>
            <div class="pager-links">

                {{-- Prev --}}
                @if($logs->onFirstPage())
                    <span class="off">‹</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}">‹</a>
                @endif

                {{-- Page numbers --}}
                @foreach($logs->getUrlRange(max(1, $logs->currentPage()-2), min($logs->lastPage(), $logs->currentPage()+2)) as $page => $url)
                    @if($page == $logs->currentPage())
                        <span class="cur">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}">›</a>
                @else
                    <span class="off">›</span>
                @endif

            </div>
        </div>
        @elseif(!$isPaginated)
        <div class="pager">
            <div class="pager-info">Showing {{ $items->count() }} records</div>
        </div>
        @endif

    @endif
<style>
/* ===== GIỮ NGUYÊN HỆ THỐNG BIẾN CỦA BẠN ===== */
:root {
    --cyan: #22d3ee;
    --green: #4ade80;
    --red: #f87171;
    --text-primary: #e2e8f0;
    --text-secondary: #64748b;
    --text-muted: #334155;
    --border-faint: rgba(148,163,184,.08);
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
}

/* ===== GIỮ NGUYÊN PHÔNG CHỮ & TIÊU ĐỀ ===== */
h1, table, td, th {
    font-family: var(--font-ui) !important;
}

.page-title {
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: -0.025em;
    font-family: var(--font-ui);
    margin-bottom: 20px;
    color: var(--text-primary);
}

.page-title em {
    font-style: normal;
    color: var(--cyan);
    font-weight: 600;
}

/* ===== THIẾT KẾ BẢNG KIỂU EXCEL CHUYÊN NGHIỆP ===== */
.bg-white {
    background: #0a1220 !important;
    border: 1px solid var(--border-faint) !important;
    border-radius: 8px !important; /* Bo nhẹ kiểu phần mềm */
    overflow: hidden;
    padding: 0 !important; /* Để bảng tràn viền card */
}

table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

/* Header phẳng kiểu Excel */
thead th {
    font-family: var(--font-mono) !important;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #CCFFFF;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 2px solid var(--border-faint);
    border-right: 1px solid var(--border-faint); /* Đường kẻ dọc */
    text-align: left;
}

/* Rows & Zebra Striping (Dòng kẻ sọc) */
tbody tr {
    border-bottom: 1px solid var(--border-faint);
    transition: .15s;
}

tbody tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.01); /* Dòng chẵn hơi tối hơn */
}

tbody tr:hover {
    background: rgba(34,211,238,.04) !important;
}

/* Cells */
td {
    padding: 12px 15px !important;
    font-size: 13px;
    color: var(--text-primary);
    border-right: 1px solid var(--border-faint); /* Đường kẻ dọc mờ */
}

/* Cột IP + Time giữ đúng phông Mono của bạn */
td:first-child,
td:last-child {
    font-family: var(--font-mono) !important;
    font-size: 11px;
    color: var(--cyan);
}

/* Type color */
.status-tag {
    font-weight: 700;
    font-size: 10px;
    text-transform: uppercase;
}

.text-red-500 { color: var(--red) !important; }
.text-green-500 { color: var(--green) !important; }

/* Description (User Agent) */
td:nth-child(5) {
    color: var(--text-secondary);
    font-size: 12px;
}

/* Bỏ đường kẻ dọc ở cột cuối cùng */
td:last-child, th:last-child {
    border-right: none;
}
/* html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: var(--surface-0) !important;
    background-image: radial-gradient(ellipse 70% 40% at 50% 0%, rgba(34,211,238,.05) 0%, transparent 55%) !important;
    color: var(--text-primary);
    font-family: var(--font-ui);
} */

.scc-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px;
    padding-top: 0 !important; /* Xóa khoảng trắng trên cùng */
}
</style>
<div class="scc-wrap">
<h1 class="page-title">
    System <em>Logs</em>
</h1>

<div class="bg-white rounded shadow">
    <table>
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Country</th>
                <th>Type</th>
                <th>Threat</th>
                <th>User Agent</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->ip }}</td>
                <td>{{ $log->country }}</td>
                <td>
                    @if($log->type == 'bot')
                        <span class="status-tag text-red-500">Bot</span>
                    @else
                        <span class="status-tag text-green-500">Human</span>
                    @endif
                </td>
                <td>
                    <span style="font-weight: 500;">{{ $log->threat ?? 'LOW' }}</span>
                </td>
                <td>{{ $log->user_agent }}</td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

@endsection