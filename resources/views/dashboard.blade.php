@extends('layouts.panel')

@section('content')

<style>

body{
background:#f1f5f9;
}

.card{
background:white;
border-radius:12px;
padding:20px;
box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.stat-card{
color:white;
border-radius:12px;
padding:20px;
}

.stat-blue{background:linear-gradient(135deg,#3b82f6,#2563eb);}
.stat-green{background:linear-gradient(135deg,#22c55e,#16a34a);}
.stat-red{background:linear-gradient(135deg,#ef4444,#dc2626);}

table{
width:100%;
font-size:14px;
}

th{
text-align:left;
padding:10px;
color:#6b7280;
}

td{
padding:10px;
}

tr{
border-bottom:1px solid #eee;
}

tr:hover{
background:#f9fafb;
}

.badge{
padding:4px 8px;
border-radius:6px;
font-size:12px;
font-weight:600;
}

.badge-human{
background:#dcfce7;
color:#15803d;
}

.badge-bot{
background:#fee2e2;
color:#dc2626;
}

.badge-low{background:#dcfce7;color:#15803d;}
.badge-medium{background:#fef9c3;color:#ca8a04;}
.badge-high{background:#fecaca;color:#b91c1c;}
.badge-critical{background:#b91c1c;color:white;}

.grid-3{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;
}

.grid-2{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:20px;
}

</style>

<div style="display:flex;flex-direction:column;gap:20px;">

<h1 style="font-size:28px;font-weight:bold;">
🛡 Security Dashboard
</h1>

<!-- STATS -->

<div class="grid-3">

<div class="stat-card stat-blue">
<div>Total Visitors</div>
<div style="font-size:32px;font-weight:bold;">
{{ $totalVisitors }}
</div>
</div>

<div class="stat-card stat-green">
<div>Human Visitors</div>
<div style="font-size:32px;font-weight:bold;">
{{ $humanVisitors }}
</div>
</div>

<div class="stat-card stat-red">
<div>Bot Visitors</div>
<div style="font-size:32px;font-weight:bold;">
{{ $botVisitors }}
</div>
</div>

</div>

<!-- TRAFFIC -->

<div class="card">

<h2 style="margin-bottom:15px;font-weight:bold;">
Traffic Chart
</h2>

<div id="trafficChart" style="height:350px;"></div>

</div>

<!-- TABLES -->

<div class="grid-2">

<div class="card">

<h2 style="margin-bottom:15px;font-weight:bold;">
🚨 Top Attacker IP
</h2>

<table>

<thead>
<tr>
<th>IP</th>
<th>Requests</th>
</tr>
</thead>

<tbody id="topIpTable"></tbody>

</table>

</div>

<div class="card">

<h2 style="margin-bottom:15px;font-weight:bold;">
🌍 Top Countries
</h2>

<table>

<thead>
<tr>
<th>Country</th>
<th>Visitors</th>
</tr>
</thead>

<tbody id="countryTable"></tbody>

</table>

</div>

</div>

<!-- MAP -->

<div class="card">

<h2 style="margin-bottom:15px;font-weight:bold;">
🌎 Global Attack Map
</h2>

<div id="attackMap" style="height:400px;"></div>

</div>

<!-- LATEST VISITORS -->

<div class="card">

<h2 style="margin-bottom:15px;font-weight:bold;">
Latest Visitors
</h2>

<table>

<thead>
<tr>
<th>IP</th>
<th>Country</th>
<th>Type</th>
<th>Threat</th>
<th>Time</th>
</tr>
</thead>

<tbody>

@foreach($latestVisitors as $v)

<tr>

<td>{{ $v->ip }}</td>

<td>🌍 {{ $v->country }}</td>

<td>

@if($v->is_bot)
<span class="badge badge-bot">Bot</span>
@else
<span class="badge badge-human">Human</span>
@endif

</td>

<td>

@if($v->threat=='CRITICAL')
<span class="badge badge-critical">CRITICAL</span>

@elseif($v->threat=='HIGH')
<span class="badge badge-high">HIGH</span>

@elseif($v->threat=='MEDIUM')
<span class="badge badge-medium">MEDIUM</span>

@else
<span class="badge badge-low">LOW</span>
@endif

</td>

<td>{{ $v->created_at }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>

<!-- SECURITY EVENTS -->

<div class="card">

<h2 style="margin-bottom:15px;font-weight:bold;">
⚠ Live Security Events
</h2>

<table>

<thead>
<tr>
<th>IP</th>
<th>Country</th>
<th>Threat</th>
<th>Time</th>
</tr>
</thead>

<tbody id="securityEvents"></tbody>

</table>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

<script>

/* CHART */

var options = {

chart:{type:'area',height:350},

series:[
{name:'Human',data:[]},
{name:'Bot',data:[]}
],

colors:['#22c55e','#ef4444'],

stroke:{curve:'smooth'},

xaxis:{categories:[]}

};

var chart = new ApexCharts(
document.querySelector("#trafficChart"),
options
);

chart.render();

function loadTraffic(){

fetch('/api/traffic-stats')
.then(res=>res.json())
.then(data=>{

let time=new Date().toLocaleTimeString();

options.series[0].data.push(data.human);
options.series[1].data.push(data.bot);

options.xaxis.categories.push(time);

chart.updateOptions(options);

});

}

setInterval(loadTraffic,5000);


/* TOP IP */

function loadTopIp(){

fetch('/api/top-ip')
.then(res=>res.json())
.then(data=>{

let html='';

data.forEach(row=>{

html+=`
<tr>
<td>${row.ip}</td>
<td style="color:red;font-weight:bold;">${row.total}</td>
</tr>
`;

});

document.getElementById('topIpTable').innerHTML=html;

});

}

loadTopIp();
setInterval(loadTopIp,10000);


/* COUNTRIES */

function loadCountries(){

fetch('/api/country-stats')
.then(res=>res.json())
.then(data=>{

let html='';

data.forEach(row=>{

html+=`
<tr>
<td>🌍 ${row.country}</td>
<td>${row.total}</td>
</tr>
`;

});

document.getElementById('countryTable').innerHTML=html;

});

}

loadCountries();
setInterval(loadCountries,10000);


/* MAP */

var map=new jsVectorMap({
selector:"#attackMap",
map:"world",
zoomButtons:true
});

function loadAttackMap(){

fetch('/api/attack-map')
.then(res=>res.json())
.then(data=>{

let regions={};

data.forEach(row=>{

if(row.country=="Vietnam")regions["VN"]=row.total;
if(row.country=="United States")regions["US"]=row.total;
if(row.country=="China")regions["CN"]=row.total;
if(row.country=="Singapore")regions["SG"]=row.total;

});

map.updateSeries([{
attribute:"fill",
values:regions
}]);

});

}

loadAttackMap();
setInterval(loadAttackMap,10000);


/* SECURITY EVENTS */

function loadSecurityEvents(){

fetch('/api/security-events')
.then(res=>res.json())
.then(data=>{

let html='';

data.forEach(row=>{

html+=`
<tr>
<td>${row.ip}</td>
<td>${row.country}</td>
<td style="color:red;font-weight:bold;">${row.threat}</td>
<td>${row.created_at}</td>
</tr>
`;

});

document.getElementById('securityEvents').innerHTML=html;

});

}

loadSecurityEvents();
setInterval(loadSecurityEvents,5000);

</script>

@endsection