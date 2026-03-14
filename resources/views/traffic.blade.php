@extends('layouts.panel')

@section('content')

<style>
/* ===== MODERN CYBERSECURITY UI VARIABLES ===== */

:root {
    --bg-main: #020617;
    --panel-bg: rgba(15, 23, 42, 0.8);
    --accent-blue: #38bdf8;
    --accent-green: #10b981;
    --accent-red: #f43f5e;
    --accent-yellow: #fbbf24;
    --text-bright: #f8fafc;
    --text-dim: #94a3b8;
    --border-color: rgba(255, 255, 255, 0.1);
}

body {
    background-color: var(--bg-main) !important;
    background-image:
        radial-gradient(circle at 50% -20%, rgba(56,189,248,0.15), transparent),
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
    background-size: 100% 100%, 40px 40px, 40px 40px;
    font-family: 'Inter',"Segoe UI",sans-serif;
    color: var(--text-bright);
}

/* ===== CARDS ===== */

.bg-white{
    background: var(--panel-bg) !important;
    backdrop-filter: blur(12px);
    border: 1px solid var(--border-color) !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4) !important;
    transition: all .3s;
}

.bg-white:hover{
    border-color: var(--accent-blue) !important;
}
/* ===== PAGE TITLE ===== */

.page-title{
    font-size:26px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:20px;
}

/* ===== CARD CONTAINER ===== */

.table-card{
    background:white;
    border-radius:14px;
    box-shadow:0 8px 25px rgba(0,0,0,0.06);
    overflow:hidden;
}

/* ===== TABLE HEADER ===== */

.table-card thead{
    background:#f1f5f9;
}

.table-card th{
    text-align:left;
    font-weight:600;
    color:#334155;
    font-size:14px;
    padding:14px 16px;
}

/* ===== TABLE BODY ===== */

.table-card td{
    padding:14px 16px;
    font-size:14px;
    color:#1e293b;
}

/* ===== ROW STYLE ===== */

.table-card tbody tr{
    border-top:1px solid #e5e7eb;
    transition:all .2s ease;
}

.table-card tbody tr:hover{
    background:#f8fafc;
    transform:scale(1.002);
}

/* ===== IP STYLE ===== */

.ip{
    font-weight:600;
    color:#2563eb;
}

/* ===== COUNTRY ===== */

.country{
    font-weight:500;
}

/* ===== BADGE ===== */

.badge{
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

/* HUMAN */

.badge-human{
    background:#dcfce7;
    color:#16a34a;
}

/* BOT */

.badge-bot{
    background:#fee2e2;
    color:#dc2626;
}

/* ===== TIME ===== */

.time{
    color:#64748b;
    font-size:13px;
}

</style>
<h1 class="page-title">
 Traffic Logs
</h1>

<div class="table-card">

<table class="w-full">

<thead>
<tr>
<th>IP Address</th>
<th>Country</th>
<th>Visitor Type</th>
<th>Time</th>
</tr>
</thead>

<tbody>

@foreach($visitors as $v)

<tr>

<td class="ip">
{{ $v->ip }}
</td>

<td class="country">
🌍 {{ $v->country }}
</td>

<td>

@if($v->is_bot)

<span class="badge badge-bot">
 Bot
</span>

@else

<span class="badge badge-human">
 Human
</span>

@endif

</td>

<td class="time">
{{ $v->created_at }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection