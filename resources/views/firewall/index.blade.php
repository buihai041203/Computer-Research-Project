@extends('layouts.panel')

@section('content')

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
button, input { font-family:var(--f-ui); }

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
   STATS CHIPS
═══════════════════════════════════════════ */
.stats-row { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }

.chip {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:99px;
    font-size:10px; font-weight:700; font-family:var(--f-mono);
    border:1px solid transparent;
}

.chip-blocked { background:var(--red-a);   color:var(--red);   border-color:rgba(248,113,113,.2); }
.chip-safe    { background:var(--green-a); color:var(--green); border-color:rgba(74,222,128,.2); }

/* ═══════════════════════════════════════════
   ALERT
═══════════════════════════════════════════ */
.alert {
    display:flex; align-items:center; gap:10px;
    padding:12px 16px; border-radius:10px;
    font-size:13px; margin-bottom:18px;
    font-family:var(--f-mono);
}

.alert-success { background:var(--green-a); border:1px solid rgba(74,222,128,.3);  color:var(--green); }
.alert-error   { background:var(--red-a);   border:1px solid rgba(248,113,113,.3); color:var(--red); }

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
    display:flex; align-items:center; justify-content:space-between; gap:12px;
}

.card-title {
    display:flex; align-items:center; gap:8px;
    font-size:13px; font-weight:700; color:#fff;
}

.c-icon {
    width:26px; height:26px; border-radius:6px;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; flex-shrink:0;
}

.card-meta {
    font-size:10px; font-weight:700;
    text-transform:uppercase; letter-spacing:.08em;
    font-family:var(--f-mono);
}

.card-body { padding:20px; }

/* ═══════════════════════════════════════════
   BLOCK FORM
═══════════════════════════════════════════ */
.block-form {
    display:grid;
    grid-template-columns:1fr 1fr auto;
    gap:12px;
    align-items:end;
}

.field { display:flex; flex-direction:column; gap:6px; }

.field-label {
    font-size:10px; font-weight:700;
    text-transform:uppercase; letter-spacing:.1em;
    color:var(--text-2);
}

.field-input {
    height:44px;
    background:rgba(255,255,255,0.04);
    border:1px solid var(--border-2);
    border-radius:10px;
    color:#fff;
    padding:0 14px;
    font-size:13px; font-family:var(--f-mono);
    transition:all .2s var(--ease);
    width:100%; outline:none;
}

.field-input::placeholder { color:var(--text-3); }

.field-input:focus {
    border-color:var(--red);
    background:rgba(248,113,113,0.05);
    box-shadow:0 0 0 3px rgba(248,113,113,0.08);
}

.field-input.err { border-color:var(--red); background:var(--red-a); }

.field-hint     { font-size:10px; color:var(--text-3); font-family:var(--f-mono); margin-top:3px; }
.field-hint.err { color:var(--red); }

.btn-block-ip {
    height:44px; padding:0 22px;
    border-radius:10px;
    background:linear-gradient(135deg, #dc2626, var(--red));
    color:#fff; font-size:13px; font-weight:700;
    border:none; cursor:pointer;
    display:inline-flex; align-items:center; gap:7px;
    white-space:nowrap;
    transition:all .2s var(--ease);
    box-shadow:0 0 0 0 rgba(248,113,113,0);
}

.btn-block-ip:hover {
    transform:translateY(-1px);
    box-shadow:0 0 18px rgba(248,113,113,0.35);
}

.btn-block-ip:active { transform:translateY(0); }

/* ═══════════════════════════════════════════
   TABLE
═══════════════════════════════════════════ */
.dt-wrap { overflow-x:auto; }

.dt { width:100%; border-collapse:collapse; min-width:580px; }

.dt thead th {
    font-size:10px; font-weight:600;
    text-transform:uppercase; letter-spacing:.08em;
    color:var(--text-3);
    padding:10px 16px; text-align:left;
    border-bottom:1px solid var(--border);
    white-space:nowrap;
}

.dt tbody tr { border-bottom:1px solid var(--border); transition:background .13s var(--ease); }
.dt tbody tr:last-child { border-bottom:none; }
.dt tbody tr:hover { background:rgba(248,113,113,0.03); }

.dt tbody td { padding:13px 16px; font-size:13px; color:var(--text); vertical-align:middle; }

.mono  { font-family:var(--f-mono); font-size:12px; }
.dim   { color:var(--text-2); }

/* IP cell */
.ip-cell { display:flex; align-items:center; gap:9px; }

.ip-av {
    width:30px; height:30px; border-radius:7px;
    display:flex; align-items:center; justify-content:center;
    font-size:11px; font-weight:700; font-family:var(--f-mono);
    background:var(--red-a); color:var(--red); flex-shrink:0;
}

/* Reason cell */
.reason-cell { display:flex; align-items:center; gap:7px; }

.reason-tag {
    display:inline-block; padding:2px 7px; border-radius:5px;
    font-size:10px; font-weight:700; font-family:var(--f-mono);
    background:var(--amber-a); color:var(--amber);
    border:1px solid rgba(251,191,36,.25);
    white-space:nowrap;
}

/* ═══════════════════════════════════════════
   UNBLOCK BUTTON
═══════════════════════════════════════════ */
.btn-unblock {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 12px; border-radius:6px;
    font-size:10px; font-weight:700; font-family:var(--f-mono);
    background:var(--green-a); color:var(--green);
    border:1px solid rgba(74,222,128,.25);
    cursor:pointer;
    transition:all .18s var(--ease);
    letter-spacing:.03em;
}

.btn-unblock:hover {
    background:rgba(74,222,128,.2);
    border-color:var(--green);
    box-shadow:0 0 10px rgba(74,222,128,.2);
    transform:translateY(-1px);
}

/* ═══════════════════════════════════════════
   EMPTY STATE
═══════════════════════════════════════════ */
.empty {
    padding:52px 20px; text-align:center;
    display:flex; flex-direction:column; align-items:center; gap:10px;
}

.empty-icon  { font-size:38px; margin-bottom:4px; }
.empty-title { font-size:14px; font-weight:600; color:var(--text); }
.empty-sub   { font-size:12px; color:var(--text-2); font-family:var(--f-mono); }

/* ═══════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════ */
@media (max-width:800px) {
    .block-form { grid-template-columns:1fr; }
    .btn-block-ip { width:100%; justify-content:center; }
    .page-header { flex-direction:column; align-items:flex-start; }
}
</style>

{{-- ════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════ --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Firewall <em>Panel</em></h1>
        <div class="page-sub">
            <span>ShieldOps</span><span>›</span>Security<span>›</span>Firewall
        </div>
    </div>

    <div class="stats-row">
        <span class="chip chip-blocked">
            <svg width="7" height="7" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
            {{ $ips->count() }} blocked
        </span>
        <span class="chip chip-safe">
            <svg width="7" height="7" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
            Firewall active
        </span>
    </div>
</div>

{{-- ════════════════════════════════════════
     ALERTS
════════════════════════════════════════ --}}
@if(session('success'))
<div class="alert alert-success">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ════════════════════════════════════════
     BLOCK IP FORM
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-head">
        <div class="card-title">
            <div class="c-icon" style="background:var(--red-a);color:var(--red)">🚫</div>
            Block IP Address
        </div>
        <span class="card-meta" style="color:var(--text-2)">Instant block · WAF enforced</span>
    </div>

    <div class="card-body">
        <form method="POST" action="/firewall/block">
            @csrf
            <div class="block-form">

                <div class="field">
                    <label class="field-label">IP Address</label>
                    <input
                        type="text"
                        name="ip"
                        class="field-input @error('ip') err @enderror"
                        placeholder="e.g. 192.168.1.100"
                        value="{{ old('ip') }}"
                        autocomplete="off"
                        required
                    >
                    @error('ip')
                        <span class="field-hint err">{{ $message }}</span>
                    @else
                        <span class="field-hint">IPv4 or IPv6 address</span>
                    @enderror
                </div>

                <div class="field">
                    <label class="field-label">Reason</label>
                    <input
                        type="text"
                        name="reason"
                        class="field-input @error('reason') err @enderror"
                        placeholder="e.g. DDoS attack, brute force..."
                        value="{{ old('reason') }}"
                    >
                    @error('reason')
                        <span class="field-hint err">{{ $message }}</span>
                    @else
                        <span class="field-hint">Optional — leave blank if unknown</span>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="btn-block-ip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Block IP
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════
     BLOCKED IPs TABLE
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-head">
        <div class="card-title">
            <div class="c-icon" style="background:var(--red-a);color:var(--red)">⛔</div>
            Blocked IP List
        </div>
        <span class="card-meta" style="color:var(--red)">{{ $ips->count() }} ENTRIES</span>
    </div>

    @if($ips->isEmpty())
        <div class="empty">
            <div class="empty-icon">🛡️</div>
            <div class="empty-title">No IPs blocked</div>
            <div class="empty-sub">Use the form above to block a suspicious IP address</div>
        </div>
    @else
        <div class="dt-wrap">
            <table class="dt">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>IP Address</th>
                        <th>Reason</th>
                        <th>Blocked At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($ips as $i => $ip)
                @php
                    $initials = strtoupper(substr($ip->ip ?? '?', 0, 2));
                    $ts = $ip->created_at instanceof \Carbon\Carbon
                          ? $ip->created_at
                          : \Carbon\Carbon::parse($ip->created_at);
                @endphp
                <tr>

                    {{-- Index --}}
                    <td class="mono dim" style="width:40px">{{ $i + 1 }}</td>

                    {{-- IP --}}
                    <td>
                        <div class="ip-cell">
                            <div class="ip-av">{{ $initials }}</div>
                            <span class="mono">{{ $ip->ip }}</span>
                        </div>
                    </td>

                    {{-- Reason --}}
                    <td>
                        <div class="reason-cell">
                            @if($ip->reason)
                                <span class="reason-tag">{{ strtoupper(Str::limit($ip->reason, 20, '')) }}</span>
                                <span class="dim" style="font-size:12px">{{ $ip->reason }}</span>
                            @else
                                <span class="dim">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Time --}}
                    <td>
                        <span class="mono dim" style="font-size:11px;white-space:nowrap">
                            {{ $ts->format('d M Y') }}
                        </span>
                        <div class="mono" style="font-size:10px;color:var(--text-3);margin-top:1px">
                            {{ $ts->format('H:i:s') }}
                        </div>
                    </td>

                    {{-- Unblock --}}
                    <td>
                        <form
                            method="POST"
                            action="/firewall/{{ $ip->id }}"
                            onsubmit="return confirmUnblock(event, '{{ $ip->ip }}')"
                            style="margin:0"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-unblock">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Unblock
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
function confirmUnblock(e, ip) {
    e.preventDefault();
    const form = e.target;

    const overlay = document.createElement('div');
    Object.assign(overlay.style, {
        position:'fixed', inset:'0', zIndex:'9998',
        background:'rgba(4,12,24,0.85)', backdropFilter:'blur(4px)',
        display:'flex', alignItems:'center', justifyContent:'center',
    });

    overlay.innerHTML = `
        <div style="background:#0a1e35;border:1px solid rgba(74,222,128,.3);border-radius:16px;padding:28px 32px;max-width:360px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.6),0 0 28px rgba(74,222,128,.08)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <div style="width:32px;height:32px;border-radius:8px;background:rgba(74,222,128,.12);display:flex;align-items:center;justify-content:center;font-size:15px">🛡️</div>
                <div style="font-size:15px;font-weight:700;color:#fff">Unblock IP</div>
            </div>
            <p style="font-size:13px;color:#64748b;margin-bottom:8px">Remove firewall block for:</p>
            <p style="font-family:'IBM Plex Mono',monospace;font-size:13px;color:#4ade80;margin-bottom:18px;padding:8px 12px;background:rgba(74,222,128,.08);border-radius:7px;border:1px solid rgba(74,222,128,.2)">
                ${ip}
            </p>
            <p style="font-size:12px;color:#64748b;margin-bottom:22px;font-family:'IBM Plex Mono',monospace">
                This IP will be allowed to access the system again immediately.
            </p>
            <div style="display:flex;gap:10px">
                <button id="cancelBtn" style="flex:1;height:40px;border-radius:8px;border:1px solid rgba(96,165,250,.2);background:rgba(96,165,250,.08);color:#60a5fa;font-size:12px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif">
                    Cancel
                </button>
                <button id="confirmBtn" style="flex:1;height:40px;border-radius:8px;border:1px solid rgba(74,222,128,.3);background:rgba(74,222,128,.15);color:#4ade80;font-size:12px;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif">
                    Yes, Unblock
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

@endsection