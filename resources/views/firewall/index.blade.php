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
    color: #CCFFFF;
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

<div class="scc-wrap" id="firewall-page-root">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; gap:10px;">
        <h1 class="page-title" style="margin:0;">
            Firewall <em>Control</em>
        </h1>

        <form method="POST" action="{{ route('firewall.auto-block') }}" onsubmit="return confirm('Chạy auto-block dựa trên traffic bất thường?');">
            @csrf
            <button class="btn-danger" type="submit">AUTO BLOCK SUSPICIOUS IPs</button>
        </form>
    </div>

    @if(session('success'))
        <div style="margin-bottom:10px; color:var(--green); font-family:var(--font-mono); font-size:11px;">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div style="margin-bottom:10px; color:var(--red); font-family:var(--font-mono); font-size:11px;">{{ $errors->first() }}</div>
    @endif

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

    <div id="firewall-live-sections">
        {{-- SUSPICIOUS IP (last 1h) --}}
        <div class="card" style="margin-bottom:20px;">
            <table class="dtable">
                <thead>
                    <tr>
                        <th colspan="4">SUSPICIOUS IPs (LAST 1 HOUR)</th>
                    </tr>
                    <tr>
                        <th>IP ADDRESS</th>
                        <th>TOTAL REQ</th>
                        <th>HIGH/CRITICAL</th>
                        <th>QUICK ACTION</th>
                    </tr>
                </thead>
                <tbody>
                @forelse(($suspicious ?? collect()) as $s)
                    <tr>
                        <td class="t-mono" style="color:var(--cyan)">{{ $s->ip }}</td>
                        <td class="t-mono">{{ $s->total }}</td>
                        <td class="t-mono" style="color:{{ ($s->risky ?? 0) > 0 ? 'var(--red)' : 'var(--text-secondary)' }}">{{ $s->risky ?? 0 }}</td>
                        <td>
                            @if($s->is_blocked ?? false)
                                <span class="btn-success" title="{{ $s->blocked_reason ?? 'Đang bị block toàn cục' }}">BLOCKED</span>
                            @else
                                <form method="POST" action="/firewall/block">
                                    @csrf
                                    <input type="hidden" name="ip" value="{{ $s->ip }}">
                                    <input type="hidden" name="reason" value="Manual quick block from suspicious list">
                                    <button class="btn-danger" type="submit">BLOCK</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:20px; font-family:var(--font-mono); color:var(--text-secondary)">
                            // NO SUSPICIOUS IP DETECTED
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- MULTI-SITE ATTACK MATRIX --}}
        <div class="card" style="margin-bottom:20px;">
            <table class="dtable">
                <thead>
                    <tr>
                        <th colspan="6">IP ATTACKING WHICH WEBSITE? (LAST 1 HOUR)</th>
                    </tr>
                    <tr>
                        <th>DOMAIN</th>
                        <th>IP ADDRESS</th>
                        <th>TOTAL REQ</th>
                        <th>HIGH/CRITICAL</th>
                        <th>LAST SEEN</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                @forelse(($ipByDomain ?? collect()) as $row)
                    <tr>
                        <td class="t-mono">{{ $row->domain ?? '-' }}</td>
                        <td class="t-mono" style="color:var(--cyan)">{{ $row->ip }}</td>
                        <td class="t-mono">{{ $row->total }}</td>
                        <td class="t-mono" style="color:{{ ($row->risky ?? 0) > 0 ? 'var(--red)' : 'var(--text-secondary)' }}">{{ $row->risky ?? 0 }}</td>
                        <td class="t-mono js-local-datetime" data-datetime="{{ \Illuminate\Support\Carbon::parse($row->last_seen)->toIso8601String() }}">{{ $row->last_seen }}</td>
                        <td>
                            @if($row->is_blocked ?? false)
                                <span class="btn-success" title="{{ $row->blocked_reason ?? 'IP đang bị block' }}">
                                    BLOCKED{{ ($row->block_scope ?? null) === 'domain' ? ' (DOMAIN)' : (($row->block_scope ?? null) === 'global' ? ' (GLOBAL)' : '') }}
                                </span>
                            @else
                                <form method="POST" action="/firewall/block">
                                    @csrf
                                    <input type="hidden" name="ip" value="{{ $row->ip }}">
                                    <input type="hidden" name="reason" value="Manual block from IP-by-domain matrix ({{ $row->domain }})">
                                    @if(!empty($row->domain_id))
                                        <input type="hidden" name="scope_type" value="domain">
                                        <input type="hidden" name="scope_value" value="{{ $row->domain_id }}">
                                    @endif
                                    <button class="btn-danger" type="submit">BLOCK</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px; font-family:var(--font-mono); color:var(--text-secondary)">
                            // NO ATTACK MATRIX DATA
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- DOMAIN ATTACK SUMMARY --}}
        <div class="card" style="margin-bottom:20px;">
            <table class="dtable">
                <thead>
                    <tr>
                        <th colspan="4">WEBSITE ATTACK SUMMARY (LAST 1 HOUR)</th>
                    </tr>
                    <tr>
                        <th>DOMAIN</th>
                        <th>TOTAL REQ</th>
                        <th>RISKY REQ</th>
                        <th>DISTINCT ATTACKER IPs</th>
                    </tr>
                </thead>
                <tbody>
                @forelse(($domainAttackSummary ?? collect()) as $row)
                    <tr>
                        <td class="t-mono">{{ $row->domain ?? '-' }}</td>
                        <td class="t-mono">{{ $row->total }}</td>
                        <td class="t-mono" style="color:{{ ($row->risky ?? 0) > 0 ? 'var(--red)' : 'var(--text-secondary)' }}">{{ $row->risky ?? 0 }}</td>
                        <td class="t-mono">{{ $row->attacker_ips ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:20px; font-family:var(--font-mono); color:var(--text-secondary)">
                            // NO DOMAIN SUMMARY DATA
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- BLOCKED TABLE --}}
        <div class="card">
            <table class="dtable">

                <thead>
                    <tr>
                        <th>IP ADDRESS</th>
                        <th>SCOPE</th>
                        <th>SOURCE</th>
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

                        <td class="t-mono">
                            <span style="color:{{ $ip->scope_type === 'domain' ? 'var(--cyan)' : 'var(--green)' }}; font-weight:700;">
                                {{ $ip->scope_label ?? strtoupper($ip->scope_type ?? 'global') }}
                            </span>
                            @if(($ip->scope_type ?? null) === 'domain' && !empty($ip->scope_domain))
                                <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">
                                    {{ $ip->scope_domain }}
                                </div>
                            @endif
                        </td>

                        <td class="t-mono" style="color:var(--text-secondary)">
                            {{ strtoupper($ip->source ?? 'manual') }}
                            @if(!empty($ip->expires_at))
                                <div style="font-size:11px; margin-top:4px;">TTL: <span class="js-local-datetime" data-datetime="{{ optional($ip->expires_at)->toIso8601String() }}">{{ $ip->expires_at }}</span></div>
                            @endif
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
                        <td colspan="5" style="text-align:center; padding:30px; font-family:var(--font-mono); color:var(--text-secondary)">
                            // NO BLOCKED IPS
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>

<script>
(function () {
    const live = document.getElementById('firewall-live-sections');
    if (!live) return;

    const intervalMs = 5000;
    let busy = false;
    const formatter = new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'medium',
    });

    function isEditing() {
        const active = document.activeElement;
        const tag = (active?.tagName || '').toLowerCase();
        return ['input', 'textarea', 'select'].includes(tag);
    }

    function applyLocalTime(scope) {
        scope.querySelectorAll('.js-local-datetime').forEach((el) => {
            const raw = el.dataset.datetime;
            if (!raw) return;
            const date = new Date(raw);
            if (Number.isNaN(date.getTime())) return;
            el.textContent = formatter.format(date);
        });
    }

    async function refreshSections() {
        if (busy || document.hidden || isEditing()) return;
        busy = true;
        try {
            const res = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const next = doc.getElementById('firewall-live-sections');
            if (next) {
                live.innerHTML = next.innerHTML;
                applyLocalTime(live);
            }
        } catch (e) {
            console.warn('[FirewallPageRefresh]', e);
        } finally {
            busy = false;
        }
    }

    applyLocalTime(live);
    window.setInterval(refreshSections, intervalMs);
})();
</script>

@endsection
