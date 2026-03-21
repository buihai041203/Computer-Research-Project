@extends('layouts.panel')

@section('content')


<style>

/* ===== FONT CHUẨN ===== */
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap');

/* ===== DESIGN SYSTEM (SYNC TRAFFIC LOGS) ===== */
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
}

/* ===== FORCE GLOBAL ===== */
body {
    font-family: var(--font-ui) !important;
    background: var(--surface-0) !important;
    color: var(--text-primary) !important;
}

/* ===== TITLE (CHỮ XANH NỬA) ===== */
h1 {
    font-size: 1.4rem !important;
    font-weight: 600 !important;
    letter-spacing: -.025em;
    margin-bottom: 24px !important;
}

/* 👉 thêm chữ xanh mà KHÔNG sửa HTML */
h1::after {
    content: " Panel";
    color: var(--cyan);
    margin-left: 6px;
}

/* ===== CARD ===== */
.bg-white {
    background: var(--surface-1) !important;
    border: 1px solid var(--border-faint) !important;
    border-radius: 14px !important;
    box-shadow: none !important;
}

/* ===== FORM ===== */
input {
    background: var(--surface-2) !important;
    border: 1px solid var(--border-faint) !important;
    color: var(--text-primary) !important;
    font-family: var(--font-mono);
    font-size: 12px;
    padding: 10px 12px !important;
}

input::placeholder {
    color: var(--text-secondary);
}

/* ===== BUTTON ===== */
button {
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 700;
    border-radius: 6px !important;
    transition: all .2s ease;
}

/* Block button */
.bg-red-500 {
    background: rgba(248,113,113,.15) !important;
    color: var(--red) !important;
    border: 1px solid rgba(248,113,113,.3);
}

.bg-red-500:hover {
    background: rgba(248,113,113,.25) !important;
}

/* Unblock button */
.text-green-500 {
    color: var(--green) !important;
    font-weight: 600;
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

/* HEADER */
thead th {
    font-family: var(--font-mono);
    font-size: 9px;
    text-transform: uppercase;
    color: var(--text-secondary);
    padding: 12px 16px !important;
    border-bottom: 1px solid var(--border-faint);
    text-align: left;
}

/* ROW */
tbody tr {
    border-bottom: 1px solid var(--border-faint);
}

tbody tr:hover {
    background: rgba(34,211,238,.02);
}

/* CELL */
td {
    padding: 14px 16px !important;
    font-size: 13px;
}

/* IP mono + màu cyan */
td:first-child {
    font-family: var(--font-mono);
    color: var(--cyan);
}

/* Reason */
td:nth-child(2) {
    color: var(--text-secondary);
}
#ai-chat-toggle,
#ai-chat-send {
    border-radius: 50% !important;
}
.logout-btn {
    font-family: var(--font-ui) !important;
    font-size: 16px !important;
    font-weight: 600 !important;
}


</style>

<h1 class="text-2xl font-bold mb-6">
    Firewall
</h1>

<div class="bg-white p-6 rounded shadow mb-6">

    <form method="POST" action="/firewall/block">
        @csrf

        <div class="flex gap-4">

            <input name="ip"
                class="border p-2 rounded w-1/3"
                placeholder="IP address">

            <input name="reason"
                class="border p-2 rounded w-1/3"
                placeholder="Reason">

            <button class="bg-red-500 text-white px-4 py-2 rounded">
                Block IP
            </button>

        </div>

    </form>

</div>

<div class="bg-white p-6 rounded shadow">

    <table class="w-full">

        <thead class="border-b">

            <tr>
                <th class="p-2 text-left">IP</th>
                <th class="p-2 text-left">Reason</th>
                <th class="p-2 text-left">Action</th>
            </tr>

        </thead>

        <tbody>

            @foreach($ips as $ip)

            <tr class="border-b">

                <td class="p-2">
                    {{ $ip->ip }}
                </td>

                <td class="p-2">
                    {{ $ip->reason }}
                </td>

                <td class="p-2">

                    <form method="POST" action="/firewall/{{ $ip->id }}">
                        @csrf
                        @method('DELETE')

                        <button class="text-green-500">
                            Unblock
                        </button>

                    </form>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection