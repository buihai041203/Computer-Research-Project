@extends('layouts.panel')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
/* ─────────────────────────────────────────────
   §1  DESIGN TOKENS  (mirrors dashboard tokens)
───────────────────────────────────────────── */
:root {
    --surface-0: #060c17;
    --surface-1: #0a1220;
    --surface-2: #0f1a2e;
    --surface-3: #162236;

    --border-faint:  rgba(148,163,184,.07);
    --border-subtle: rgba(148,163,184,.13);

    --teal:       #00e5c0;
    --teal-dim:   rgba(0,229,192,.08);
    --teal-mid:   rgba(0,229,192,.18);
    --green:      #4ade80;
    --green-dim:  rgba(74,222,128,.09);
    --red:        #f87171;
    --red-dim:    rgba(248,113,113,.09);
    --amber:      #fbbf24;

    --text-primary:   #dde4ef;
    --text-secondary: #64748b;
    --text-muted:     #2d3f5c;

    --mono: 'Space Mono', monospace;
    --ui:   'DM Sans', sans-serif;

    --r-sm: 5px;
    --r-md: 9px;
    --r-lg: 14px;
    --ease: cubic-bezier(.4,0,.2,1);
    --dur:  160ms;
}

/* ─────────────────────────────────────────────
   §2  PAGE SHELL
───────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: var(--ui);
    background: var(--surface-0);
    color: var(--text-primary);
}

/* ─────────────────────────────────────────────
   §3  PAGE HEADER
───────────────────────────────────────────── */
.log-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.log-header__left {}

.log-title {
    font-size: 1.3rem;
    font-weight: 600;
    letter-spacing: -.02em;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 4px;
}

.log-title__icon {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--teal);
    box-shadow: 0 0 8px var(--teal);
    flex-shrink: 0;
    animation: dot-pulse 2.4s ease-in-out infinite;
}

@keyframes dot-pulse {
    0%,100% { opacity:.6; transform:scale(.85); }
    50%      { opacity:1;  transform:scale(1.25); box-shadow: 0 0 10px var(--teal); }
}

.log-subtitle {
    font-family: var(--mono);
    font-size: 10px;
    color: var(--text-secondary);
    letter-spacing: .06em;
}

/* ─────────────────────────────────────────────
   §4  TOOLBAR  (search + filters)
───────────────────────────────────────────── */
.toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.search-wrap {
    position: relative;
}

.search-wrap__icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    font-family: var(--mono);
    font-size: 10px;
    color: var(--text-muted);
    pointer-events: none;
    user-select: none;
}

.search-input {
    background: var(--surface-2);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-md);
    color: var(--text-primary);
    font-family: var(--mono);
    font-size: 11px;
    padding: 8px 12px 8px 34px;
    outline: none;
    width: 220px;
    transition: border-color var(--dur) var(--ease), box-shadow var(--dur) var(--ease);
}

.search-input::placeholder { color: var(--text-muted); }
.search-input:focus {
    border-color: var(--teal-mid);
    box-shadow: 0 0 0 3px rgba(0,229,192,.06);
}

.filter-btn {
    background: var(--surface-2);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-md);
    color: var(--text-secondary);
    font-family: var(--mono);
    font-size: 10px;
    letter-spacing: .06em;
    padding: 8px 14px;
    cursor: pointer;
    transition: all var(--dur) var(--ease);
    text-transform: uppercase;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--teal-dim);
    border-color: var(--teal-mid);
    color: var(--teal);
}

.filter-btn.active--red {
    background: var(--red-dim);
    border-color: rgba(248,113,113,.25);
    color: var(--red);
}

/* ─────────────────────────────────────────────
   §5  SUMMARY STRIP
───────────────────────────────────────────── */
.summary-strip {
    display: flex;
    gap: 0;
    margin-bottom: 16px;
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    overflow: hidden;
    background: var(--surface-1);
}

.summary-item {
    flex: 1;
    padding: 14px 18px;
    border-right: 1px solid var(--border-faint);
    position: relative;
    overflow: hidden;
}
.summary-item:last-child { border-right: none; }

.summary-item__val {
    font-family: var(--mono);
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 3px;
}
.summary-item--total  .summary-item__val { color: var(--text-primary); }
.summary-item--human  .summary-item__val { color: var(--green); }
.summary-item--bot    .summary-item__val { color: var(--red); }

.summary-item__label {
    font-family: var(--mono);
    font-size: 9px;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.summary-item__bar {
    position: absolute;
    bottom: 0; left: 0;
    height: 2px;
    border-radius: 0 2px 2px 0;
    transition: width .8s var(--ease);
}
.summary-item--total .summary-item__bar  { background: var(--teal); }
.summary-item--human .summary-item__bar  { background: var(--green); }
.summary-item--bot   .summary-item__bar  { background: var(--red); }

/* ─────────────────────────────────────────────
   §6  TABLE CARD
───────────────────────────────────────────── */
.log-card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    overflow: hidden;
    transition: border-color var(--dur) var(--ease);
}
.log-card:hover { border-color: var(--border-subtle); }

/* Sticky header wrapper */
.table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.log-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 640px;
}

/* ─────────────────────────────────────────────
   §7  TABLE HEADER
───────────────────────────────────────────── */
.log-table thead tr {
    background: var(--surface-2);
    border-bottom: 1px solid var(--border-faint);
}

.log-table thead th {
    font-family: var(--mono);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 11px 16px;
    text-align: left;
    white-space: nowrap;
    user-select: none;
    cursor: pointer;
    transition: color var(--dur) var(--ease);
}
.log-table thead th:hover { color: var(--text-secondary); }
.log-table thead th.sort-active { color: var(--teal); }

.th-inner {
    display: flex;
    align-items: center;
    gap: 5px;
}
.th-inner::after {
    content: '↕';
    font-size: 8px;
    opacity: .35;
}
.sort-active .th-inner::after { content: '↓'; opacity: .8; }

/* ─────────────────────────────────────────────
   §8  TABLE BODY
───────────────────────────────────────────── */
.log-table tbody tr {
    border-bottom: 1px solid var(--border-faint);
    transition: background var(--dur) var(--ease);
    position: relative;
}
.log-table tbody tr:last-child { border-bottom: none; }
.log-table tbody tr:hover { background: rgba(0,229,192,.025); }

/* Left edge glow on hover — bot rows */
.log-table tbody tr.row--bot:hover { background: rgba(248,113,113,.03); }

.log-table tbody td {
    padding: 13px 16px;
    font-size: 13px;
    vertical-align: middle;
}

/* IP column */
.td-ip {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text-primary);
    font-weight: 700;
    white-space: nowrap;
}

/* Row index */
.td-index {
    font-family: var(--mono);
    font-size: 9px;
    color: var(--text-muted);
    width: 40px;
    padding-right: 8px;
    text-align: right;
    user-select: none;
}

/* Country column */
.td-country {
    font-size: 12px;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}

/* Type badge */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: var(--r-sm);
    font-family: var(--mono);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    white-space: nowrap;
}

.badge--human {
    background: var(--green-dim);
    color: var(--green);
    border: 1px solid rgba(74,222,128,.2);
}
.badge--human::before { content: '●'; font-size: 6px; }

.badge--bot {
    background: var(--red-dim);
    color: var(--red);
    border: 1px solid rgba(248,113,113,.2);
}
.badge--bot::before { content: '●'; font-size: 6px; }

/* Time column */
.td-time {
    font-family: var(--mono);
    font-size: 10px;
    color: var(--text-secondary);
    white-space: nowrap;
}

.td-time abbr {
    text-decoration: none;
    border-bottom: 1px dashed var(--text-muted);
    cursor: default;
}

/* ─────────────────────────────────────────────
   §9  EMPTY STATE
───────────────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 56px 32px;
}
.empty-state__icon {
    font-family: var(--mono);
    font-size: 1.5rem;
    color: var(--text-muted);
    margin-bottom: 12px;
    letter-spacing: .2em;
}
.empty-state__text {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text-muted);
    letter-spacing: .06em;
}

/* ─────────────────────────────────────────────
   §10  PAGINATION
───────────────────────────────────────────── */
.pager {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    border-top: 1px solid var(--border-faint);
    background: var(--surface-2);
}

.pager__meta {
    font-family: var(--mono);
    font-size: 9px;
    letter-spacing: .08em;
    color: var(--text-muted);
    text-transform: uppercase;
}

.pager__links { display: flex; gap: 6px; align-items: center; }

.pager__links a,
.pager__links span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 8px;
    border-radius: var(--r-sm);
    font-family: var(--mono);
    font-size: 10px;
    text-decoration: none;
    transition: all var(--dur) var(--ease);
    border: 1px solid transparent;
    color: var(--text-secondary);
}

.pager__links a:hover {
    background: var(--surface-3);
    border-color: var(--border-subtle);
    color: var(--text-primary);
}

.pager__links .active {
    background: var(--teal-dim);
    border-color: var(--teal-mid);
    color: var(--teal);
    font-weight: 700;
}

.pager__links .disabled {
    opacity: .3;
    pointer-events: none;
}

/* ─────────────────────────────────────────────
   §11  SCROLLBAR
───────────────────────────────────────────── */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border-subtle); border-radius: 99px; }

/* ─────────────────────────────────────────────
   §12  RESPONSIVE
───────────────────────────────────────────── */
@media (max-width: 768px) {
    .log-header { flex-direction: column; align-items: flex-start; }
    .toolbar { width: 100%; }
    .search-input { width: 100%; }
    .summary-strip { flex-direction: column; }
    .summary-item { border-right: none; border-bottom: 1px solid var(--border-faint); }
    .summary-item:last-child { border-bottom: none; }
}
</style>

{{-- ════════════════════════════════════════════
     COMPUTE COUNTS FOR SUMMARY STRIP
════════════════════════════════════════════ --}}
@php
    $total     = count($visitors);
    $botCount  = $visitors->where('is_bot', true)->count();
    $humanCount= $total - $botCount;
    $botPct    = $total > 0 ? round($botCount  / $total * 100) : 0;
    $humanPct  = $total > 0 ? round($humanCount / $total * 100) : 0;
@endphp

{{-- ════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════ --}}
<div class="log-header">
    <div class="log-header__left">
        <h1 class="log-title">
            <span class="log-title__icon" aria-hidden="true"></span>
            Traffic Logs
        </h1>
        <p class="log-subtitle">Detailed request history · visitor identity · geo origin</p>
    </div>

    {{-- Toolbar --}}
    <div class="toolbar" role="search" aria-label="Filter traffic logs">
        <div class="search-wrap">
            <span class="search-wrap__icon" aria-hidden="true">/&gt;</span>
            <input
                class="search-input"
                type="search"
                id="js-search"
                placeholder="Search IP, country…"
                aria-label="Search logs"
                autocomplete="off"
                spellcheck="false"
            >
        </div>

        <button class="filter-btn active" data-filter="all"    aria-pressed="true">All</button>
        <button class="filter-btn"        data-filter="human"  aria-pressed="false">Human</button>
        <button class="filter-btn active--red" data-filter="bot" aria-pressed="false">Bot</button>
    </div>
</div>

{{-- ════════════════════════════════════════════
     SUMMARY STRIP
════════════════════════════════════════════ --}}
<div class="summary-strip" role="region" aria-label="Log summary">

    <div class="summary-item summary-item--total">
        <p class="summary-item__val">{{ number_format($total) }}</p>
        <p class="summary-item__label">Total requests</p>
        <div class="summary-item__bar" style="width:100%"></div>
    </div>

    <div class="summary-item summary-item--human">
        <p class="summary-item__val">{{ number_format($humanCount) }}</p>
        <p class="summary-item__label">Human · {{ $humanPct }}%</p>
        <div class="summary-item__bar" style="width:{{ $humanPct }}%"></div>
    </div>

    <div class="summary-item summary-item--bot">
        <p class="summary-item__val">{{ number_format($botCount) }}</p>
        <p class="summary-item__label">Bot · {{ $botPct }}%</p>
        <div class="summary-item__bar" style="width:{{ $botPct }}%"></div>
    </div>

</div>

{{-- ════════════════════════════════════════════
     LOG TABLE
════════════════════════════════════════════ --}}
<div class="log-card" role="region" aria-label="Traffic log entries">
    <div class="table-scroll">
        <table class="log-table" id="js-log-table" aria-label="Traffic logs">
            <thead>
                <tr>
                    <th class="td-index" aria-label="Row number">#</th>
                    <th data-col="ip">
                        <div class="th-inner">IP Address</div>
                    </th>
                    <th data-col="country">
                        <div class="th-inner">Country</div>
                    </th>
                    <th data-col="type">
                        <div class="th-inner">Visitor Type</div>
                    </th>
                    <th data-col="time">
                        <div class="th-inner">Time</div>
                    </th>
                </tr>
            </thead>
            <tbody id="js-tbody">

            @forelse($visitors as $i => $v)
            @php
                $rowClass = $v->is_bot ? 'row--bot' : 'row--human';
            @endphp
            <tr
                class="{{ $rowClass }}"
                data-type="{{ $v->is_bot ? 'bot' : 'human' }}"
                data-ip="{{ strtolower($v->ip) }}"
                data-country="{{ strtolower($v->country) }}"
            >
                <td class="td-index">{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</td>

                <td class="td-ip">{{ $v->ip }}</td>

                <td>
                    <span class="td-country">
                        <span aria-hidden="true">🌍</span>
                        {{ $v->country }}
                    </span>
                </td>

                <td>
                    @if($v->is_bot)
                        <span class="badge badge--bot" aria-label="Visitor type: Bot">Bot</span>
                    @else
                        <span class="badge badge--human" aria-label="Visitor type: Human">Human</span>
                    @endif
                </td>

                <td class="td-time">
                    <abbr title="{{ $v->created_at->format('Y-m-d H:i:s') }}">
                        {{ $v->created_at->diffForHumans() }}
                    </abbr>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state" role="status">
                        <p class="empty-state__icon">// —</p>
                        <p class="empty-state__text">No traffic records found</p>
                    </div>
                </td>
            </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($visitors instanceof \Illuminate\Pagination\LengthAwarePaginator && $visitors->hasPages())
    <div class="pager" role="navigation" aria-label="Log pagination">
        <span class="pager__meta">
            Showing {{ $visitors->firstItem() }}–{{ $visitors->lastItem() }}
            of {{ number_format($visitors->total()) }} entries
        </span>
        <div class="pager__links">
            {{-- Previous --}}
            @if($visitors->onFirstPage())
                <span class="disabled" aria-disabled="true">← Prev</span>
            @else
                <a href="{{ $visitors->previousPageUrl() }}" rel="prev" aria-label="Previous page">← Prev</a>
            @endif

            {{-- Page numbers --}}
            @foreach($visitors->getUrlRange(
                max(1, $visitors->currentPage() - 2),
                min($visitors->lastPage(), $visitors->currentPage() + 2)
            ) as $page => $url)
                @if($page === $visitors->currentPage())
                    <span class="active" aria-current="page" aria-label="Page {{ $page }}">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($visitors->hasMorePages())
                <a href="{{ $visitors->nextPageUrl() }}" rel="next" aria-label="Next page">Next →</a>
            @else
                <span class="disabled" aria-disabled="true">Next →</span>
            @endif
        </div>
    </div>
    @else
    <div class="pager">
        <span class="pager__meta">
            {{ $total }} {{ Str::plural('entry', $total) }} total
        </span>
    </div>
    @endif

</div>

<script>
'use strict';

/* ══════════════════════════════════════════════════
   CLIENT-SIDE FILTER + SEARCH
   Works on the already-rendered DOM rows so we don't
   need an extra API round-trip for simple filtering.
══════════════════════════════════════════════════ */
(function initLogFilter() {
    const tbody       = document.getElementById('js-tbody');
    const searchInput = document.getElementById('js-search');
    const filterBtns  = document.querySelectorAll('[data-filter]');
    const rows        = Array.from(tbody.querySelectorAll('tr[data-type]'));

    let activeFilter = 'all';
    let searchQuery  = '';

    function applyFilters() {
        let visibleCount = 0;

        rows.forEach(row => {
            const matchType    = activeFilter === 'all' || row.dataset.type === activeFilter;
            const searchTarget = (row.dataset.ip + ' ' + row.dataset.country).toLowerCase();
            const matchSearch  = !searchQuery || searchTarget.includes(searchQuery);
            const show         = matchType && matchSearch;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        // Show empty state if nothing matches
        const existingEmpty = tbody.querySelector('.js-no-results');
        if (visibleCount === 0 && !existingEmpty) {
            const tr = document.createElement('tr');
            tr.className = 'js-no-results';
            tr.innerHTML = `
                <td colspan="5">
                    <div class="empty-state" role="status">
                        <p class="empty-state__icon">// —</p>
                        <p class="empty-state__text">No results match your filter</p>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        } else if (visibleCount > 0 && existingEmpty) {
            existingEmpty.remove();
        }
    }

    /* Filter buttons */
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            activeFilter = btn.dataset.filter;
            filterBtns.forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-pressed', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
            applyFilters();
        });
    });

    /* Search — debounced 200 ms */
    let debounceTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            searchQuery = searchInput.value.trim().toLowerCase();
            applyFilters();
        }, 200);
    });

    /* Re-number visible rows after filter */
    function renumberRows() {
        let n = 1;
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const idx = row.querySelector('.td-index');
                if (idx) idx.textContent = String(n++).padStart(3, '0');
            }
        });
    }

    /* Extend applyFilters to also renumber */
    const _orig = applyFilters;
    (function override() {
        applyFilters = function() { _orig(); renumberRows(); };
    })();

})();
</script>

@endsection