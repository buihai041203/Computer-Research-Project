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

/* ===== TITLE ===== */

h1{
    font-size:1.8rem!important;
    font-weight:800!important;
    background: linear-gradient(to right,#fff,var(--accent-blue));
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

h2{
    color:var(--accent-blue)!important;
    font-size:12px!important;
    text-transform:uppercase;
    letter-spacing:1px;
    font-weight:700!important;
}

/* ===== STATS ===== */

.text-3xl{
    font-family:'JetBrains Mono',monospace;
    font-size:34px!important;
}

/* ===== TABLE ===== */

table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 8px;
}

thead th{
    color:var(--text-dim);
    font-size:11px;
    text-transform:uppercase;
    padding:10px;
}

tbody td{
    background:rgba(255,255,255,0.03);
    padding:12px;
}

tbody tr:hover td{
    background:rgba(56,189,248,0.1);
}

tbody td:first-child{
border-radius:10px 0 0 10px;
}

tbody td:last-child{
border-radius:0 10px 10px 0;
}

/* ===== BADGES ===== */

.badge{
padding:4px 10px;
border-radius:6px;
font-size:11px;
font-weight:700;
}

.badge-bot{
background:rgba(244,63,94,0.15);
color:var(--accent-red);
border:1px solid var(--accent-red);
}

.badge-human{
background:rgba(16,185,129,0.15);
color:var(--accent-green);
border:1px solid var(--accent-green);
}

.badge-critical{
background:var(--accent-red);
color:white;
box-shadow:0 0 10px var(--accent-red);
}

.live-indicator{
width:8px;
height:8px;
background:var(--accent-green);
border-radius:50%;
display:inline-block;
margin-right:8px;
box-shadow:0 0 10px var(--accent-green);
animation:pulse 1.5s infinite;
}

@keyframes pulse{
0%{transform:scale(.9);opacity:.7;}
50%{transform:scale(1.2);opacity:1;}
100%{transform:scale(.9);opacity:.7;}
}

table th,
table td{
text-align:left !important;
}
/* ===== SYSTEM MONITORING ADD-ON ===== */
.sys-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 1.25rem !important;
}

.sys-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--border-color);
}

.progress-container {
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    margin-top: 8px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease;
}
</style>



<!-- HEADER -->

<div class="mb-8 flex justify-between items-end">

<div>
<h1 class="mb-1">5M SECURITY DASHBOARD</h1>
<p class="text-sm text-dim">Intelligent Monitoring & Alert System</p>
</div>

<div class="text-right text-xs text-dim">
Last sync: <span id="last-sync">--:--:--</span>
</div>

</div>



<!-- STATS -->

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

<div class="bg-white p-6 border-b-4 border-blue-500">
<h2>Total Visitors</h2>
<div class="text-3xl mt-2">{{ number_format($totalVisitors) }}</div>
</div>

<div class="bg-white p-6 border-b-4 border-green-500">
<h2>Human Traffic</h2>
<div class="text-3xl text-green-500 mt-2">{{ number_format($humanVisitors) }}</div>
</div>

<div class="bg-white p-6 border-b-4 border-red-500">
<h2>Bot Detection</h2>
<div class="text-3xl text-red-500 mt-2">{{ number_format($botVisitors) }}</div>
</div>

</div>



<!-- TRAFFIC CHART -->

<div class="bg-white p-6 mb-8">

<h2 class="mb-6 flex items-center">
<span class="live-indicator"></span> Real-time Traffic Analysis
</h2>

<div id="trafficChart" style="height:350px;"></div>

</div>



<!-- MAP FULL WIDTH -->

<div class="bg-white p-6 mb-8">

<h2 class="mb-4">Live Threat Map</h2>

<div id="attackMap" style="height:420px;"></div>

</div>



<!-- TOP TABLES -->

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

<div class="bg-white p-6">

<h2 class="mb-4">Top Attacker IP</h2>

<table>

<thead>
<tr>
<th>IP ADDRESS</th>
<th>REQUESTS</th>
</tr>
</thead>

<tbody id="topIpTable"></tbody>

</table>

</div>



<div class="bg-white p-6">

<h2 class="mb-4">Global Traffic Origin</h2>

<table>

<thead>
<tr>
<th>COUNTRY</th>
<th>VISITORS</th>
</tr>
</thead>

<tbody id="countryTable"></tbody>

</table>

</div>

</div>



<!-- LATEST EVENTS -->

<div class="bg-white p-6 mt-8">

<h2 class="mb-6">Latest Network Events</h2>

<table>

<thead>
<tr>
<th>IP</th>
<th>COUNTRY</th>
<th>TYPE</th>
<th>THREAT</th>
<th>TIME</th>
</tr>
</thead>

<tbody>

@foreach($latestVisitors as $v)

<tr>

<td class="font-mono text-blue-400">{{ $v->ip }}</td>

<td>🌍 {{ $v->country }}</td>

<td>

@if($v->is_bot)
<span class="badge badge-bot">Bot</span>
@else
<span class="badge badge-human">Human</span>
@endif

</td>

<td>

@if($v->threat == 'CRITICAL')
<span class="badge badge-critical">CRITICAL</span>

@elseif($v->threat == 'HIGH')
<span class="text-red-500 font-bold">HIGH</span>

@elseif($v->threat == 'MEDIUM')
<span class="text-yellow-500">MEDIUM</span>

@else
<span class="text-green-500">LOW</span>
@endif

</td>

<td class="text-xs text-dim">{{ $v->created_at->diffForHumans() }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>



<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>



<script>

/* TIME */
function updateTime(){
document.getElementById('last-sync').innerText =
new Date().toLocaleTimeString();
}

/* CHART */

var options={
chart:{type:'area',height:350,toolbar:{show:false}},
series:[{name:'Human',data:[]},{name:'Bot',data:[]}],
xaxis:{categories:[]},
colors:['#10b981','#f43f5e'],
stroke:{curve:'smooth',width:3}
};

var chart=new ApexCharts(
document.querySelector("#trafficChart"),
options
);

chart.render();

function loadTraffic(){

fetch('/api/traffic-stats')
.then(res=>res.json())
.then(data=>{

let time=new Date().toLocaleTimeString([],{
hour:'2-digit',minute:'2-digit'
});

options.series[0].data.push(data.human);
options.series[1].data.push(data.bot);

options.xaxis.categories.push(time);

if(options.series[0].data.length>10){

options.series[0].data.shift();
options.series[1].data.shift();
options.xaxis.categories.shift();

}

chart.updateOptions(options);
updateTime();

});

}

setInterval(loadTraffic,5000);


/* MAP */

var map=new jsVectorMap({
selector:"#attackMap",
map:"world",
regionStyle:{
initial:{fill:"rgba(255,255,255,0.1)"}
}
});


/* TOP IP */

function loadTopIp(){

fetch('/api/top-ip')
.then(res=>res.json())
.then(data=>{

let html=data.map(row=>`

<tr>
<td class="font-mono text-blue-300">${row.ip}</td>
<td class="font-bold text-red-400">${row.total}</td>
</tr>

`).join('');

document.getElementById('topIpTable').innerHTML=html;

});

}

setInterval(loadTopIp,10000);
loadTopIp();

</script>

@endsection