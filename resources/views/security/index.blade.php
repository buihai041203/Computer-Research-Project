@extends('layouts.panel')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>

/* ===== IMPORT FONT (đã có) ===== */

/* ===== DESIGN TOKENS ===== */
:root {
    --surface-0: #060c17;
    --surface-1: #0a1220;

    --border-faint: rgba(148,163,184,.07);
    --border-subtle: rgba(148,163,184,.13);

    --red: #f87171;

    --text-primary: #e2e8f0;
    --text-secondary: #64748b;
    --text-muted: #2d3f5c;

    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
}

/* ===== BODY ===== */
body {
    font-family: var(--font-ui) !important;
    background: var(--surface-0);
    color: var(--text-primary);
}

/* ===== TITLE ===== */
h1 {
    font-size: 1.4rem !important;
    font-weight: 600 !important;
    margin-bottom: 24px !important;
    letter-spacing: -0.02em;
}

/* ===== CARD ===== */
.bg-white {
    background: var(--surface-1) !important;
    border: 1px solid var(--border-faint) !important;
    border-radius: 14px !important;
    overflow: hidden;
}

.bg-white:hover {
    border-color: var(--border-subtle) !important;
    box-shadow: 0 8px 30px rgba(0,0,0,.4);
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

/* ===== HEADER (FIX LỆCH FONT + SPACING) ===== */
thead th {
    font-family: var(--font-mono) !important;
    font-size: 9px !important;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 10px 16px !important;
    text-align: left;
    border-bottom: 1px solid var(--border-faint);
}

/* ===== ROW ===== */
tbody tr {
    border-bottom: 1px solid var(--border-faint) !important;
    transition: background .2s ease;
}

tbody tr:hover {
    background: rgba(34,211,238,.03);
}

/* ===== CELL (FIX PADDING TAILWIND) ===== */
td {
    padding: 11px 16px !important;
    font-size: 13px;
    color: var(--text-primary);
}

/* ===== MONO (IP + TIME) ===== */
td:first-child,
td:last-child {
    font-family: var(--font-mono) !important;
    font-size: 11px;
}

/* ===== TYPE ===== */
td:nth-child(2) {
    font-weight: 600;
    color: var(--red) !important;
}

/* ===== DESCRIPTION ===== */
td:nth-child(3) {
    color: var(--text-secondary);
}

/* ===== FIX TAILWIND LỆCH ===== */
.p-3 {
    padding: 11px 16px !important;
}

.border-b {
    border-bottom: 1px solid var(--border-faint) !important;
}
.page-title {
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: -0.025em;
}

.page-title em {
    font-style: normal;
    color: #22d3ee; /* cyan giống dashboard */
}
</style>

<h1 class="page-title">
    Security <em>Events</em>
</h1>

<table class="w-full bg-white shadow">

<thead>

<tr class="border-b">

<th class="p-3">IP</th>
<th class="p-3">Type</th>
<th class="p-3">Description</th>
<th class="p-3">Time</th>

</tr>

</thead>

<tbody>

@foreach($events as $event)

<tr class="border-b">

<td class="p-3">{{ $event->ip }}</td>

<td class="p-3 text-red-500">
{{ $event->type }}
</td>

<td class="p-3">
{{ $event->description }}
</td>

<td class="p-3">
{{ $event->created_at }}
</td>

</tr>

@endforeach

</tbody>

</table>

@endsection
