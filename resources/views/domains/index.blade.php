@extends('layouts.panel')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     DOMAIN MANAGEMENT · v2.1 (Stable GET Toggle)
     Sync: Security Command Center Design System
══════════════════════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
/* ─────────────────────────────────────────────────────
   §1  SHARED DESIGN TOKENS
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
    background-image: radial-gradient(ellipse 70% 40% at 50% 0%, rgba(34,211,238,.05) 0%, transparent 55%) !important;
    color: var(--text-primary);
    font-family: var(--font-ui);
}

.card {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: var(--r-lg);
    transition: all var(--dur) var(--ease);
    overflow: hidden;
    margin-bottom: 24px;
}
.card:hover { border-color: rgba(34,211,238,0.2); box-shadow: 0 8px 32px rgba(0,0,0,.35); }

.card__header { padding: 16px 20px; border-bottom: 1px solid var(--border-faint); }
.card__body { padding: 20px; }

.t-label { font-size: 10px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--text-secondary); }
.t-mono { font-family: var(--font-mono); font-size: 12px; }

.page-header { margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--border-faint); }
.page-header__title { font-size: 1.4rem; font-weight: 600; letter-spacing: -.025em; }
.page-header__title em { font-style: normal; color: var(--cyan); }

.form-group { display: flex; gap: 12px; }
.input-cyber {
    flex: 1; background: var(--surface-2); border: 1px solid var(--border-faint);
    color: var(--text-primary); padding: 12px 16px; border-radius: var(--r-sm);
    font-family: var(--font-mono); font-size: 13px;
}
.btn-cyber {
    background: var(--cyan); color: #060c17; border: none; padding: 0 24px;
    border-radius: var(--r-sm); font-weight: 700; font-size: 12px; cursor: pointer;
}

/* TABLE STYLES */
.dtable { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-faint); }
.dtable thead th {
    font-family: var(--font-mono); font-size: 11px; text-transform: uppercase;
    color: #CCFFFF; padding: 16px 15px; background: rgba(15, 23, 42, 0.9);
    border-bottom: 2px solid rgba(34, 211, 238, 0.2); text-align: left;
}
.dtable td { padding: 14px 15px; font-size: 13px; color: var(--text-primary); border-bottom: 1px solid var(--border-faint); }

/* BADGES */
.badge { padding: 3px 10px; border-radius: 4px; font-family: var(--font-mono); font-size: 10px; font-weight: 700; }
.badge--active { background: rgba(74,222,128,.1); color: var(--green); border: 1px solid rgba(74,222,128,.2); }
.badge--pending { background: rgba(251,191,36,.1); color: var(--amber); border: 1px solid rgba(251,191,36,.2); }
.badge--offline { background: rgba(248,113,113,.1); color: var(--red); border: 1px solid rgba(248,113,113,.2); }

.action-btn { background: transparent; border: none; font-family: var(--font-mono); font-size: 10px; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn-delete { color: var(--text-secondary); }
.btn-delete:hover { color: var(--red); }

</style>

<div class="scc-wrap" style="max-width: 1200px; margin: 0 auto; padding: 24px;">

    <header class="page-header">
        <h1 class="page-header__title">Domain <em>Management</em></h1>
        <p class="t-label" style="margin-top:4px; color:var(--text-secondary)">Monitor and secure your endpoints</p>
    </header>

    @if(session('success'))
        <div class="card" style="background: rgba(74,222,128,.05); border-color: rgba(74,222,128,.2); color: var(--green); padding: 12px 20px; font-size: 13px; margin-bottom: 20px;">
            <span class="t-mono">✓</span> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card__header"><span class="t-label">Register New Domain</span></div>
        <div class="card__body">
            <form method="POST" action="/domains" class="form-group">
                @csrf
                <input name="domain" class="input-cyber" placeholder="secure-site.com" required>
                <input type="hidden" name="php_version" value="8.4">
                <button type="submit" class="btn-cyber">Add Domain</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card__header"><span class="t-label">Active Subscriptions</span></div>
        <div class="card__body" style="padding:0">
            <table class="dtable">
                <thead>
                    <tr>
                        <th>Domain Name</th>
                        <th>Root Path</th>
                        <th>PHP</th>
                        <th>Status</th>
                        <th>Management</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($domains as $domain)
                    <tr>
                        <td class="t-mono" style="color:var(--cyan)">{{ $domain->domain }}</td>
                        <td class="t-mono" style="font-size:11px; color:var(--text-secondary)">{{ $domain->root_path }}</td>
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
                                <form action="/domains/{{ $domain->id }}" method="POST" style="margin: 0;" onsubmit="return confirm('Xác nhận xóa domain?');">
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