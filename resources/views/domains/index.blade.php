@extends('layouts.panel')


@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════
   TOKENS — shared với dashboard
═══════════════════════════════════════════ */
:root {
    --bg:       #040c18;
    --surface:  #071525;
    --card:     #0a1e35;
    --card-2:   #0d2440;

    --border:   rgba(96,165,250,0.10);
    --border-2: rgba(96,165,250,0.22);
    --border-3: rgba(96,165,250,0.45);

    --blue:   #60a5fa;
    --cyan:   #22d3ee;
    --green:  #4ade80;
    --red:    #f87171;
    --amber:  #fbbf24;

    --blue-a:  rgba(96,165,250,0.12);
    --green-a: rgba(74,222,128,0.12);
    --red-a:   rgba(248,113,113,0.12);
    --amber-a: rgba(251,191,36,0.10);

    --text:   #cbd5e1;
    --text-2: #64748b;
    --text-3: #1e3a5f;

    --f-ui:   'Outfit', sans-serif;
    --f-mono: 'IBM Plex Mono', monospace;
    --ease:   cubic-bezier(.4,0,.2,1);
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

*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
button, input { font-family:var(--f-ui); }

body {
    font-family:var(--f-ui);
    background:var(--bg);
    color:var(--text);
    font-size:14px;
    -webkit-font-smoothing:antialiased;
}

::-webkit-scrollbar { width:4px; }
::-webkit-scrollbar-thumb { background:var(--border-2); border-radius:99px; }

/* ═══════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════ */
.page-header { margin-bottom:28px; }

.page-title {
    font-size:1.5rem;
    font-weight:800;
    color:#fff;
    letter-spacing:-.03em;
    line-height:1.1;
}

.page-title em { font-style:normal; color:var(--cyan); }

.page-sub {
    margin-top:5px;
    font-size:12px;
    color:var(--text-2);
    font-family:var(--f-mono);
    display:flex;
    align-items:center;
    gap:8px;
}

/* ═══════════════════════════════════════════
   ALERT
═══════════════════════════════════════════ */
.alert {
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 16px;
    border-radius:10px;
    font-size:13px;
    margin-bottom:20px;
    font-family:var(--f-mono);
}

.alert-success {
    background:var(--green-a);
    border:1px solid rgba(74,222,128,.3);
    color:var(--green);
}

.alert-error {
    background:var(--red-a);
    border:1px solid rgba(248,113,113,.3);
    color:var(--red);
}

/* ═══════════════════════════════════════════
   CARD
═══════════════════════════════════════════ */
.card {
    background:var(--card);
    border:1px solid var(--border);
    border-radius:18px;
    overflow:hidden;
    margin-bottom:18px;
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
    font-size:12px;
    flex-shrink:0;
}

.card-meta {
    font-size:10px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.08em;
    font-family:var(--f-mono);
    color:var(--text-2);
}

.card-body { padding:20px; }

/* ═══════════════════════════════════════════
   FORM — ADD DOMAIN
═══════════════════════════════════════════ */
.form-grid {
    display:grid;
    grid-template-columns:1fr auto;
    gap:12px;
    align-items:end;
}

.field { display:flex; flex-direction:column; gap:6px; }

.field-label {
    font-size:10px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.1em;
    color:var(--text-2);
}

.field-input {
    height:44px;
    background:rgba(255,255,255,0.04);
    border:1px solid var(--border-2);
    border-radius:10px;
    color:#fff;
    padding:0 14px;
    font-size:13px;
    font-family:var(--f-mono);
    transition:all .2s var(--ease);
    width:100%;
}

.field-input::placeholder { color:var(--text-3); }

.field-input:focus {
    outline:none;
    border-color:var(--cyan);
    background:rgba(34,211,238,0.05);
    box-shadow:0 0 0 3px rgba(34,211,238,0.08);
}

.field-input.error {
    border-color:var(--red);
    background:var(--red-a);
}

.field-hint {
    font-size:10px;
    color:var(--text-3);
    font-family:var(--f-mono);
    margin-top:3px;
}

.field-hint.err { color:var(--red); }

.btn-primary {
    height:44px;
    padding:0 22px;
    border-radius:10px;
    background:linear-gradient(135deg, var(--cyan), var(--blue));
    color:#fff;
    font-size:13px;
    font-weight:700;
    border:none;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:7px;
    white-space:nowrap;
    transition:all .2s var(--ease);
    box-shadow:0 0 0 0 rgba(34,211,238,0);
}

.btn-primary:hover {
    transform:translateY(-1px);
    box-shadow:0 0 18px rgba(34,211,238,0.3);
}

.btn-primary:active { transform:translateY(0); }

/* ═══════════════════════════════════════════
   DATA TABLE
═══════════════════════════════════════════ */
.dt { width:100%; border-collapse:collapse; }

.dt thead th {
    font-size:10px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:var(--text-3);
    padding:0 16px 10px;
    text-align:left;
    border-bottom:1px solid var(--border);
    white-space:nowrap;
}

.dt thead th.right { text-align:right; }

.dt tbody tr {
    border-bottom:1px solid var(--border);
    transition:background .15s var(--ease);
}

.dt tbody tr:last-child { border-bottom:none; }
.dt tbody tr:hover { background:rgba(96,165,250,0.04); }

.dt tbody td {
    padding:13px 16px;
    font-size:13px;
    color:var(--text);
    vertical-align:middle;
}

.dt tbody td.right {
    text-align:right;
}

.mono { font-family:var(--f-mono); font-size:12px; }
.dim  { color:var(--text-2); }

/* domain name cell */
.domain-cell {
    display:flex;
    align-items:center;
    gap:9px;
}

.domain-globe {
    width:28px; height:28px;
    border-radius:7px;
    background:var(--blue-a);
    display:flex; align-items:center; justify-content:center;
    font-size:13px;
    flex-shrink:0;
}

.domain-name {
    font-weight:600;
    color:#fff;
    font-size:13px;
}

.domain-id {
    font-family:var(--f-mono);
    font-size:10px;
    color:var(--text-3);
    margin-top:1px;
}

/* ═══════════════════════════════════════════
   BADGES
═══════════════════════════════════════════ */
.badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:4px 10px;
    border-radius:6px;
    font-size:10px;
    font-weight:700;
    font-family:var(--f-mono);
    letter-spacing:.04em;
    white-space:nowrap;
    border:1px solid transparent;
}

.b-active  { background:var(--green-a); color:var(--green); border-color:rgba(74,222,128,.25); }
.b-pending { background:var(--amber-a); color:var(--amber); border-color:rgba(251,191,36,.25); }
.b-offline { background:var(--red-a);   color:var(--red);   border-color:rgba(248,113,113,.25); }

/* ═══════════════════════════════════════════
   ACTION BUTTONS
═══════════════════════════════════════════ */
.action-group {
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:6px;
}

.btn-toggle {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 11px;
    border-radius:6px;
    font-size:10px;
    font-weight:700;
    font-family:var(--f-mono);
    border:1px solid transparent;
    cursor:pointer;
    transition:all .18s var(--ease);
    background:none;
}

.btn-toggle.on {
    background:var(--green-a);
    color:var(--green);
    border-color:rgba(74,222,128,.25);
}

.btn-toggle.on:hover {
    background:rgba(74,222,128,.2);
    border-color:var(--green);
}

.btn-toggle.off {
    background:var(--red-a);
    color:var(--red);
    border-color:rgba(248,113,113,.25);
}

.btn-toggle.off:hover {
    background:rgba(248,113,113,.2);
    border-color:var(--red);
}

.btn-delete {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 11px;
    border-radius:6px;
    font-size:10px;
    font-weight:700;
    font-family:var(--f-mono);
    background:transparent;
    color:var(--text-2);
    border:1px solid var(--border);
    cursor:pointer;
    transition:all .18s var(--ease);
}

.btn-delete:hover {
    background:var(--red-a);
    color:var(--red);
    border-color:rgba(248,113,113,.3);
}

/* ═══════════════════════════════════════════
   EMPTY STATE
═══════════════════════════════════════════ */
.empty-state {
    padding:52px 20px;
    text-align:center;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:10px;
}

.empty-icon {
    width:48px; height:48px;
    border-radius:14px;
    background:var(--blue-a);
    display:flex; align-items:center; justify-content:center;
    font-size:22px;
    margin-bottom:4px;
}

.empty-title { font-size:14px; font-weight:600; color:var(--text); }
.empty-sub   { font-size:12px; color:var(--text-2); font-family:var(--f-mono); }

/* ═══════════════════════════════════════════
   COUNT PILL
═══════════════════════════════════════════ */
.count-pill {
    background:var(--blue-a);
    color:var(--blue);
    border:1px solid rgba(96,165,250,.2);
    border-radius:99px;
    font-size:11px;
    font-weight:700;
    padding:2px 10px;
    font-family:var(--f-mono);
}

/* ═══════════════════════════════════════════
   TOAST
═══════════════════════════════════════════ */
.toast {
    position:fixed; bottom:22px; right:22px; z-index:9999;
    padding:12px 18px; border-radius:10px;
    background:var(--card-2); border:1px solid var(--green); color:var(--green);
    font-family:var(--f-mono); font-size:12px;
    box-shadow:0 8px 32px rgba(0,0,0,.45),0 0 18px rgba(74,222,128,.12);
    animation:t-in .28s var(--ease) both;
}

.toast.err { border-color:var(--red); color:var(--red); }

@keyframes t-in {
    from { opacity:0; transform:translateY(10px) scale(.97); }
    to   { opacity:1; transform:none; }
}

/* ═══════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════ */
@media (max-width:700px) {
    .form-grid { grid-template-columns:1fr; }
    .btn-primary { width:100%; justify-content:center; }
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

{{-- ════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════ --}}
<div class="page-header">
    <h1 class="page-title">Domain <em>Management</em></h1>
    <div class="page-sub">
        <span>ShieldOps</span>
        <span style="color:var(--text-3)">›</span>
        Domains
        <span style="color:var(--text-3)">›</span>
        Active Subscriptions
    </div>
</div>

{{-- ════════════════════════════════════════
     SESSION ALERTS
════════════════════════════════════════ --}}
@if(session('success'))
<div class="alert alert-success">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- ════════════════════════════════════════
     REGISTER NEW DOMAIN
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-head">
        <div class="card-title">
            <div class="c-icon" style="background:var(--blue-a);color:var(--cyan)">+</div>
            Register New Domain
        </div>
        <span class="card-meta">Protected via ShieldOps WAF</span>
    </div>

    <div class="card-body">
        <form method="POST" action="/domains">
            @csrf
            <input type="hidden" name="php_version" value="8.4">

            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Target Domain</label>
                    <input
                        type="text"
                        name="domain"
                        class="field-input @error('domain') error @enderror"
                        placeholder="e.g. secure-site.com"
                        value="{{ old('domain') }}"
                        autocomplete="off"
                        required
                    >
                    @error('domain')
                        <span class="field-hint err">{{ $message }}</span>
                    @else
                        <span class="field-hint">Enter the root domain without http:// or trailing slash</span>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="btn-primary">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Domain
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════
     ACTIVE SUBSCRIPTIONS TABLE
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-head">
        <div class="card-title">
            <div class="c-icon" style="background:var(--blue-a);color:var(--blue)">🌐</div>
            Active Subscriptions
        </div>
        <span class="count-pill">{{ $domains->count() }} domains</span>
    </div>

    @if($domains->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🌐</div>
            <div class="empty-title">No domains registered yet</div>
            <div class="empty-sub">Add your first domain above to start monitoring</div>
        </div>
    @else
        <table class="dt">
            <thead>
                <tr>
                    <th>Domain Name</th>
                    <th>Root Path</th>
                    <th>PHP</th>
                    <th>Status</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($domains as $domain)
            <tr>
                {{-- Domain --}}
                <td>
                    <div class="domain-cell">
                        <div class="domain-globe">🌐</div>
                        <div>
                            <div class="domain-name">{{ $domain->domain }}</div>
                            <div class="domain-id mono">#{{ $domain->id }}</div>
                        </div>
                    </div>
                </td>

                {{-- Root Path --}}
                <td class="mono dim">{{ $domain->root_path ?? '—' }}</td>

                {{-- PHP Version --}}
                <td>
                    <span class="badge" style="background:var(--blue-a);color:var(--blue);border-color:rgba(96,165,250,.2)">
                        PHP {{ $domain->php_version ?? '8.4' }}
                    </span>
                </td>

                {{-- Status --}}
                <td>
                    @if($domain->status === 'pending_setup')
                        <span class="badge b-pending">
                            <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                            Pending
                        </span>
                    @elseif(($domain->is_active ?? true) === false)
                        <span class="badge b-offline">
                            <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                            Offline
                        </span>
                    @else
                        <span class="badge b-active">
                            <svg width="7" height="7" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                            {{ ucfirst($domain->status ?? 'Active') }}
                        </span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="right">
                    <div class="action-group">

                        {{-- Toggle ON/OFF --}}
                        <form action="/domains/{{ $domain->id }}/toggle" method="POST" style="margin:0">
                            @csrf
                            <button
                                type="submit"
                                class="btn-toggle {{ ($domain->is_active ?? true) ? 'on' : 'off' }}"
                                title="{{ ($domain->is_active ?? true) ? 'Click to disable' : 'Click to enable' }}"
                            >
                                @if($domain->is_active ?? true)
                                    <svg width="9" height="9" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                                    ON
                                @else
                                    <svg width="9" height="9" viewBox="0 0 8 8" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="4" cy="4" r="3"/></svg>
                                    OFF
                                @endif
                            </button>
                        </form>

                        {{-- Delete --}}
                        <form
                            action="/domains/{{ $domain->id }}"
                            method="POST"
                            style="margin:0"
                            onsubmit="return confirmDelete(event, '{{ $domain->domain }}')"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" title="Delete domain">
                                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>
function confirmDelete(e, domain) {
    e.preventDefault();
    const form = e.target;

    // Custom confirm UI
    const overlay = document.createElement('div');
    Object.assign(overlay.style, {
        position:'fixed', inset:'0', zIndex:'9998',
        background:'rgba(4,12,24,0.85)', backdropFilter:'blur(4px)',
        display:'flex', alignItems:'center', justifyContent:'center',
    });

    overlay.innerHTML = `
        <div style="background:#0a1e35;border:1px solid rgba(248,113,113,.3);border-radius:16px;padding:28px 32px;max-width:380px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.6),0 0 30px rgba(248,113,113,.1)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <div style="width:32px;height:32px;border-radius:8px;background:rgba(248,113,113,.12);display:flex;align-items:center;justify-content:center;font-size:15px">⚠</div>
                <div style="font-size:15px;font-weight:700;color:#fff">Delete Domain</div>
            </div>
            <p style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:6px">
                You are about to permanently delete:
            </p>
            <p style="font-family:'IBM Plex Mono',monospace;font-size:13px;color:#f87171;margin-bottom:18px;padding:8px 12px;background:rgba(248,113,113,.08);border-radius:7px;border:1px solid rgba(248,113,113,.2)">
                ${domain}
            </p>
            <p style="font-size:12px;color:#64748b;margin-bottom:22px;font-family:'IBM Plex Mono',monospace">
                This will permanently delete all associated logs and data. This action cannot be undone.
            </p>
            <div style="display:flex;gap:10px">
                <button id="cancelBtn" style="flex:1;height:40px;border-radius:8px;border:1px solid rgba(96,165,250,.2);background:rgba(96,165,250,.08);color:#60a5fa;font-size:12px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif">
                    Cancel
                </button>
                <button id="confirmBtn" style="flex:1;height:40px;border-radius:8px;border:none;background:#f87171;color:#fff;font-size:12px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:opacity .2s">
                    Yes, Delete
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);
    document.getElementById('cancelBtn').onclick  = () => overlay.remove();
    document.getElementById('confirmBtn').onclick = () => { overlay.remove(); form.submit(); };

    return false;
}
</script>

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