@extends('layouts.panel')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     TRAFFIC LOGS · v2.0
     Sync: Security Command Center / Domain Management Design System
══════════════════════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
/* ─────────────────────────────────────────────────────
   §1  SHARED DESIGN TOKENS (Đồng bộ tuyệt đối)
───────────────────────────────────────────────────── */
:root {
    --surface-0: #060c17;
    --surface-1: #0a1220;
    --surface-2: #0f1a2e;
    --surface-3: #162236;
    --border-faint: rgba(148,163,184,.07);
    --cyan: #22d3ee;
    --green: #4ade80;
    --red: #f87171;
    --amber: #fbbf24;
    --text-primary: #e2e8f0;
    --text-secondary: #64748b;
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
    --r-lg: 14px;
    --r-sm: 6px;
    --ease: cubic-bezier(.4,0,.2,1);
    --dur: 180ms;
}

/* ÉP SÁT LÊN ĐỈNH 100% - NHƯ TRANG DOMAIN */
html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: var(--surface-0) !important;
    background-image: radial-gradient(ellipse 70% 40% at 50% 0%, rgba(34,211,238,.05) 0%, transparent 55%) !important;
    color: var(--text-primary);
    font-family: var(--font-ui);
}

.scc-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px;
    padding-top: 0 !important; /* Xóa khoảng trắng trên cùng */
}

/* ─────────────────────────────────────────────────────
   §2  PAGE HEADER SYNC
───────────────────────────────────────────────────── */
.page-header {
    margin-top: 0 !important;
    padding-top: 8px !important;
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-faint);
    display: flex;
    justify-content: space-between;
    align-items: center;
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

/* ─────────────────────────────────────────────────────
   §3  SUMMARY STRIP SYNC
───────────────────────────────────────────────────── */
.summary-strip {
    display: flex;
    margin-bottom: 24px;
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    overflow: hidden;
}

.summary-item {
    flex: 1;
    padding: 16px 20px;
    border-right: 1px solid var(--border-faint);
}
.summary-item:last-child { border-right: none; }

.summary-item__val {
    font-family: var(--font-mono);
    font-size: 1.4rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
}

.summary-item__label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-secondary);
}

/* ─────────────────────────────────────────────────────
   §4  SEARCH & FILTER SYNC
───────────────────────────────────────────────────── */
.toolbar {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}

.input-cyber {
    background: var(--surface-2);
    border: 1px solid var(--border-faint);
    color: var(--text-primary);
    padding: 8px 14px;
    border-radius: var(--r-sm);
    font-family: var(--font-mono);
    font-size: 12px;
    width: 240px;
}

.filter-btn {
    background: var(--surface-2);
    border: 1px solid var(--border-faint);
    color: var(--text-secondary);
    padding: 8px 16px;
    border-radius: var(--r-sm);
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    text-transform: uppercase;
    transition: all var(--dur) var(--ease);
}

.filter-btn:hover, .filter-btn.active {
    border-color: var(--cyan);
    color: var(--cyan);
}

/* ─────────────────────────────────────────────────────
   §5  TABLE SYNC (dtable style)
───────────────────────────────────────────────────── */
/* TABLE STYLE giống System Logs */
.dtable {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0;
}

/* Header */
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

/* Row */
.dtable tbody tr {
    border-bottom: 1px solid var(--border-faint);
    transition: .15s;
}

/* Zebra rows */
.dtable tbody tr:nth-child(even) {
    background: rgba(255,255,255,0.01);
}

/* Hover */
.dtable tbody tr:hover {
    background: rgba(34,211,238,.04);
}

/* Cell */
.dtable td {
    padding: 12px 15px;
    font-size: 13px;
    color: var(--text-primary);
    border-right: 1px solid var(--border-faint);
}

/* Remove last border */
.dtable td:last-child,
.dtable th:last-child {
    border-right: none;
}
.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    overflow: hidden;
}

.t-mono { font-family: var(--font-mono); font-size: 12px; }

/* Badges */
.badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-family: var(--font-mono);
    font-size: 9px;
    font-weight: 700;
}
.badge--human { background: rgba(74,222,128,.1); color: var(--green); border: 1px solid rgba(74,222,128,.2); }
.badge--bot { background: rgba(248,113,113,.1); color: var(--red); border: 1px solid rgba(248,113,113,.2); }

/* Pagination Sync */
.pager {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--border-faint);
    background: var(--surface-2);
}
.scc-wrap {
    max-width: 1440px;
    margin: 0 auto;
    padding: 24px;
}
</style>

<div class="scc-wrap">

    {{-- PAGE HEADER --}}
    <header class="page-header">
        <div>
            <h1 class="page-header__title">Traffic <em>Logs</em></h1>
            <p style="font-size: 10px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-top: 4px;">
                Detailed request history · visitor identity · geo origin
            </p>
        </div>
        
        <div class="toolbar" style="align-items:center;">
            <input type="text" id="js-search" class="input-cyber" placeholder="SEARCH IP / COUNTRY...">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="human">Human</button>
            <button class="filter-btn" data-filter="bot" style="color:var(--red)">Bot</button>

            <form method="POST" action="{{ route('traffic.clear') }}" onsubmit="return confirm('Xóa toàn bộ traffic logs?');" style="margin-left:6px;">
                @csrf
                <button type="submit" class="filter-btn" style="border-color: rgba(248,113,113,.35); color: var(--red);">
                    Clear Logs
                </button>
            </form>
        </div>
    </header>

    @if(session('success'))
        <div style="margin-bottom:12px; color:var(--green); font-family:var(--font-mono); font-size:11px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div style="margin-bottom:12px; color:var(--red); font-family:var(--font-mono); font-size:11px;">{{ session('error') }}</div>
    @endif

    @php
        $total = count($visitors);
        $botCount = $visitors->where('is_bot', true)->count();
        $humanCount = $total - $botCount;
        $botPct = $total > 0 ? round($botCount / $total * 100) : 0;
        $humanPct = $total > 0 ? round($humanCount / $total * 100) : 0;
    @endphp

    {{-- SUMMARY STRIP --}}
    <div class="summary-strip">
        <div class="summary-item">
            <p class="summary-item__val" style="color:var(--text-primary)">{{ number_format($total) }}</p>
            <p class="summary-item__label">Total requests</p>
        </div>
        <div class="summary-item">
            <p class="summary-item__val" style="color:var(--green)">{{ number_format($humanCount) }}</p>
            <p class="summary-item__label">Human · {{ $humanPct }}%</p>
        </div>
        <div class="summary-item">
            <p class="summary-item__val" style="color:var(--red)">{{ number_format($botCount) }}</p>
            <p class="summary-item__label">Bot · {{ $botPct }}%</p>
        </div>
    </div>

    {{-- LOG TABLE CARD --}}
    <div class="card">
        <table class="dtable" id="js-log-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>IP Address</th>
                    <th>Country</th>
                    <th>Visitor Type</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="js-tbody">
                @forelse($visitors as $v)
                <tr data-type="{{ $v->is_bot ? 'bot' : 'human' }}" 
                    data-ip="{{ strtolower($v->ip) }}" 
                    data-country="{{ strtolower($v->country) }}">
                    <td style="font-family: var(--font-mono); font-size: 10px; color: var(--text-secondary)">{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="t-mono" style="color:var(--cyan)">{{ $v->ip }}</td>
                    <td>
                        <span style="font-size: 13px;">🌍 {{ $v->country }}</span>
                    </td>
                    <td>
                        @if($v->is_bot)
                            <span class="badge badge--bot">BOT</span>
                        @else
                            <span class="badge badge--human">HUMAN</span>
                        @endif
                    </td>
                    <td class="t-mono" style="font-size: 10px; color: var(--text-secondary)">
                        {{ $v->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-secondary); font-family: var(--font-mono);">
                        // NO RECORDS FOUND
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        @if($visitors instanceof \Illuminate\Pagination\LengthAwarePaginator && $visitors->hasPages())
        <div class="pager">
            <span style="font-family: var(--font-mono); font-size: 9px; color: var(--text-secondary)">
                SHOWING {{ $visitors->firstItem() }}–{{ $visitors->lastItem() }} OF {{ $visitors->total() }}
            </span>
            <div style="display: flex; gap: 8px;">
                {{-- Đơn giản hóa pagination link để giữ đồng bộ thiết kế --}}
                {!! $visitors->links() !!}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
// Giữ nguyên logic JS của bạn không thay đổi
'use strict';
(function initLogFilter() {
    const tbody = document.getElementById('js-tbody');
    const searchInput = document.getElementById('js-search');
    const filterBtns = document.querySelectorAll('[data-filter]');
    const rows = Array.from(tbody.querySelectorAll('tr[data-type]'));
    let activeFilter = 'all';
    let searchQuery = '';

    function applyFilters() {
        let visibleCount = 0;
        rows.forEach(row => {
            const matchType = activeFilter === 'all' || row.dataset.type === activeFilter;
            const searchTarget = (row.dataset.ip + ' ' + row.dataset.country).toLowerCase();
            const matchSearch = !searchQuery || searchTarget.includes(searchQuery);
            const show = matchType && matchSearch;
            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        renumberRows();
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            activeFilter = btn.dataset.filter;
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilters();
        });
    });

    searchInput.addEventListener('input', () => {
        searchQuery = searchInput.value.trim().toLowerCase();
        applyFilters();
    });

    function renumberRows() {
        let n = 1;
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const idx = row.cells[0];
                if (idx) idx.textContent = String(n++).padStart(3, '0');
            }
        });
    }
})();
</script>

@endsection