@extends('layouts.panel')

@section('content')
<style>

/* ===== SYNC WITH PANEL (NO GLOBAL OVERRIDE) ===== */

/* KHÔNG đụng body nữa */
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

/* ===== FORCE FONT ĐỒNG BỘ ===== */
h1, table, td, th {
    font-family: var(--font-ui) !important;
}

/* ===== CARD ===== */
.bg-white {
    background: #0a1220 !important;
    border: 1px solid var(--border-faint) !important;
    border-radius: 14px !important;
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

/* header */
thead th {
    font-family: var(--font-mono) !important;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    padding: 12px;
    border-bottom: 1px solid var(--border-faint);
}

/* rows */
tbody tr {
    border-bottom: 1px solid var(--border-faint);
    transition: .2s;
}

tbody tr:hover {
    background: rgba(34,211,238,.04);
}

/* cells */
td {
    padding: 12px !important;
    font-size: 13px;
    color: var(--text-primary);
}

/* IP + Time mono */
td:first-child,
td:last-child {
    font-family: var(--font-mono) !important;
    font-size: 11px;
}

/* type color */
.text-red-500 {
    color: var(--red) !important;
}

.text-green-500 {
    color: var(--green) !important;
}

/* description */
td:nth-child(5) {
    color: var(--text-secondary);
}

/* title */
h1 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
}
.page-title {
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: -0.025em;
    font-family: 'DM Sans', sans-serif;
}

.page-title em {
    font-style: normal;
    color: #22d3ee; /* xanh cyan */
    font-weight: 600;
}

</style>

<h1 class="page-title">
    System <em>Logs</em>
</h1>

<div class="bg-white p-6 rounded shadow">

<table class="w-full">

<thead class="border-b">

<tr>
<th class="p-2 text-left">IP</th>
<th class="p-2 text-left">Country</th>
<th class="p-2 text-left">Type</th>
<th class="p-2 text-left">Threat</th>
<th class="p-2 text-left">User Agent</th>
<th class="p-2 text-left">Time</th>
</tr>

</thead>

<tbody>

@foreach($logs as $log)

<tr class="border-b">

<td class="p-2">
{{ $log->ip }}
</td>

<td class="p-2">
🌍 {{ $log->country }}
</td>

<td class="p-2">

@if($log->type == 'bot')
<span class="text-red-500">Bot</span>
@else
<span class="text-green-500">Human</span>
@endif

</td>

<td class="p-2">
{{ $log->threat ?? 'LOW' }}
</td>

<td class="p-2 text-sm">
{{ $log->user_agent }}
</td>

<td class="p-2">
{{ $log->created_at }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection
