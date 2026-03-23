@extends('layouts.panel')


@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     DOMAIN MANAGEMENT · v2.0
     Sync: Security Command Center Design System
     Fonts: Space Mono + DM Sans
══════════════════════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
/* ─────────────────────────────────────────────────────
   §1  SHARED DESIGN TOKENS (Đồng bộ với Dashboard)
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

body {
    background: var(--surface-0) !important;
    background-image: 
        radial-gradient(ellipse 70% 40% at 50% 0%, rgba(34,211,238,.05) 0%, transparent 55%) !important;
    color: var(--text-primary);
    font-family: var(--font-ui);
}

/* ─────────────────────────────────────────────────────
   §2  REUSABLE COMPONENTS
───────────────────────────────────────────────────── */
.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    transition: all var(--dur) var(--ease);
    overflow: hidden;
    margin-bottom: 24px;
}
.card:hover { border-color: rgba(148,163,184,.13); box-shadow: 0 8px 32px rgba(0,0,0,.35); }

.card__header { padding: 16px 20px; border-bottom: 1px solid var(--border-faint); }
.card__body { padding: 20px; }

/* ─────────────────────────────────────────────────────
   §3  TYPOGRAPHY & UI
───────────────────────────────────────────────────── */
.t-label { font-size: 10px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--text-secondary); }
.t-section { font-size: 13px; font-weight: 600; color: var(--text-primary); }
.t-mono { font-family: var(--font-mono); font-size: 12px; }

.page-header { margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--border-faint); }
.page-header__title { font-size: 1.4rem; font-weight: 600; letter-spacing: -.025em; }
.page-header__title em { font-style: normal; color: var(--cyan); }

/* ─────────────────────────────────────────────────────
   §4  FORMS & INPUTS
───────────────────────────────────────────────────── */
.form-group { display: flex; gap: 12px; }

.input-cyber {
    flex: 1;
    background: var(--surface-2);
    border: 1px solid var(--border-faint);
    color: var(--text-primary);
    padding: 12px 16px;
    border-radius: var(--r-sm);
    font-family: var(--font-mono);
    font-size: 13px;
    transition: all var(--dur) var(--ease);
}
.input-cyber:focus {
    outline: none;
    border-color: var(--cyan);
    background: var(--surface-3);
    box-shadow: 0 0 0 1px var(--cyan);
}

.btn-cyber {
    background: var(--cyan);
    color: #060c17;
    border: none;
    padding: 0 24px;
    border-radius: var(--r-sm);
    font-weight: 700;
    font-size: 12px;
    cursor: pointer;
    transition: all var(--dur) var(--ease);
    text-transform: uppercase;
}
.btn-cyber:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 0 15px rgba(34,211,238,0.3); }

/* ─────────────────────────────────────────────────────
   §5  SYNCED DATA TABLE
───────────────────────────────────────────────────── */
/* TỔNG THỂ BẢNG */
.dtable {
    width: 100%;
    border-collapse: separate; /* Đổi thành separate để bo góc hoạt động chuẩn */
    border-spacing: 0;
    background: rgba(10, 18, 32, 0.5); /* Nền nhẹ cho bảng */
    border: 1px solid var(--border-faint);
    border-radius: 12px;
    overflow: hidden; /* Để nội dung không tràn khỏi góc bo */
}

/* HEADER - NỔI BẬT VỚI CHỮ CYAN */
.dtable thead th {
    font-family: var(--font-mono) !important;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #CCFFFF;
    padding: 16px 15px;
    background: rgba(15, 23, 42, 0.9);
    border-bottom: 2px solid rgba(34, 211, 238, 0.2); /* Viền dưới màu cyan mờ */
    border-right: 1px solid var(--border-faint);
    text-align: left;
    text-shadow: 0 0 8px rgba(34, 211, 238, 0.3); /* Hiệu ứng phát sáng nhẹ cho chữ */
}

/* CELL - NỘI DUNG */
.dtable td {
    padding: 14px 15px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    border-right: 1px solid var(--border-faint);
    border-bottom: 1px solid var(--border-faint);
    transition: all 0.2s ease;
}

/* DÒNG & HIỆU ỨNG HOVER */
.dtable tbody tr {
    transition: all 0.2s ease;
}

.dtable tbody tr:last-child td {
    border-bottom: none; /* Dòng cuối cùng không có viền dưới */
}

.dtable tbody tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.015);
}

.dtable tbody tr:hover {
    background: rgba(34, 211, 238, 0.04) !important;
    backdrop-filter: blur(4px); /* Hiệu ứng kính mờ khi hover */
}

/* LOẠI BỎ VIỀN PHẢI CHO CỘT CUỐI */
.dtable td:last-child,
.dtable th:last-child {
    border-right: none;
}

/* ĐỊNH DẠNG LINK TRONG BẢNG */
.dtable td a {
    color: var(--cyan);
    text-decoration: none;
    font-family: var(--font-mono);
    font-size: 12px;
    transition: 0.2s;
}

.dtable td a:hover {
    color: #fff;
    text-shadow: 0 0 10px var(--cyan);
}
/* Status Badges */
.badge {
    padding: 3px 10px; border-radius: 4px; font-family: var(--font-mono); font-size: 10px; font-weight: 700;
}
.badge--active { background: rgba(74,222,128,.1); color: var(--green); border: 1px solid rgba(74,222,128,.2); }
.badge--pending { background: rgba(251,191,36,.1); color: var(--amber); border: 1px solid rgba(251,191,36,.2); }

.action-btn {
    background: transparent; border: none; font-family: var(--font-mono);
    font-size: 10px; font-weight: 700; cursor: pointer; text-transform: uppercase;
}
.btn-delete { color: var(--text-secondary); transition: color .2s; }
.btn-delete:hover { color: var(--red); }
</style>

<div class="scc-wrap" style="max-width: 1200px; margin: 0 auto; padding: 24px;">

    {{-- PAGE HEADER --}}
    <header class="page-header">
        <h1 class="page-header__title">Domain <em>Management</em></h1>
        <p class="t-label" style="margin-top:4px; color:var(--text-secondary)">Add and monitor your protected web domains</p>
    </header>

    @if(session('success'))
        <div class="card" style="background: rgba(74,222,128,.05); border-color: rgba(74,222,128,.2); color: var(--green); padding: 12px 20px; font-size: 13px;">
            <span class="t-mono">✓</span> {{ session('success') }}
        </div>
    @endif

    {{-- REGISTER FORM --}}
    <div class="card">
        <div class="card__header">
            <span class="t-label">Register New Domain</span>
        </div>
        <div class="card__body">
            <form method="POST" action="/domains" class="form-group">
                @csrf
                <input name="domain" class="input-cyber" placeholder="e.g. secure-site.com" required>
                <input type="hidden" name="php_version" value="8.4">
                <button type="submit" class="btn-cyber">Add Domain</button>
            </form>
        </div>
    </div>

    {{-- ACTIVE SUBSCRIPTIONS --}}
    <div class="card">
        <div class="card__header">
            <span class="t-label">Active Subscriptions</span>
        </div>
        <div class="card__body" style="padding:0">
            <table class="dtable">
                <thead>
                    <tr>
                        <th>Domain Name</th>
                        <th>Root Path</th>
                        <th>PHP</th>
                        <th>Status</th>
                        <th >Management</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($domains as $domain)
                    <tr>
                        <td class="t-mono" style="color:var(--cyan)">{{ $domain->domain }}</td>
                        <td class="t-mono" style="font-size:11px; color:var(--text-secondary)">{{ $domain->root_path ?? '/var/www/html' }}</td>
                        <td class="t-mono">{{ $domain->php_version ?? '8.4' }}</td>
                        <td>
                            @if($domain->status == 'pending_setup')
                                <span class="badge badge--pending">Pending</span>
                            @else
                                <span class="badge badge--active">Active</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:20px; align-items:center;">
                                {{-- Toggle --}}
                                <form action="/domains/{{ $domain->id }}/toggle" method="POST">
                                    @csrf
                                    <button type="submit" class="action-btn"
                                        style="color: {{ ($domain->is_active ?? true) ? 'var(--green)' : 'var(--red)' }}">
                                        {{ ($domain->is_active ?? true) ? '● ON' : '○ OFF' }}
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form action="/domains/{{ $domain->id }}" method="POST"
                                    onsubmit="return confirm('Xác nhận xóa domain này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection