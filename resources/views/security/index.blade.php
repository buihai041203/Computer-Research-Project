@extends('layouts.panel')

@section('content')

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

<div class="scc-wrap" id="security-page-root">

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

    <div class="card" id="security-events-card">
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

<script>
(function () {
    const root = document.getElementById('security-page-root');
    const card = document.getElementById('security-events-card');
    if (!root || !card) return;

    const intervalMs = 5000;
    let busy = false;

    async function refreshCard() {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const res = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const next = doc.getElementById('security-events-card');
            if (next) card.innerHTML = next.innerHTML;
        } catch (e) {
            console.warn('[SecurityPageRefresh]', e);
        } finally {
            busy = false;
        }
    }

    window.setInterval(refreshCard, intervalMs);
})();
</script>

@endsection