@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
Dashboard
</h1>

<div class="grid grid-cols-3 gap-6 mb-6">

<div class="bg-white p-6 rounded shadow">
<h2 class="text-gray-500">Total Visitors</h2>
<div class="text-3xl font-bold">
{{ $totalVisitors }}
</div>
</div>

<div class="bg-white p-6 rounded shadow">
<h2 class="text-gray-500">Human Visitors</h2>
<div class="text-3xl font-bold text-green-500">
{{ $humanVisitors }}
</div>
</div>

<div class="bg-white p-6 rounded shadow">
<h2 class="text-gray-500">Bot Visitors</h2>
<div class="text-3xl font-bold text-red-500">
{{ $botVisitors }}
</div>
</div>

</div>

<div class="bg-white p-6 rounded shadow">

<h2 class="mb-4 font-bold">
Traffic Chart
</h2>

<div id="trafficChart" style="height:350px;"></div>

</div>

<div class="bg-white p-6 rounded shadow mt-6">

<h2 class="font-bold mb-4">
Top Attacker IP
</h2>

<table class="w-full">

<thead class="border-b">
<tr>
<th class="p-2 text-left">IP</th>
<th class="p-2 text-left">Requests</th>
</tr>
</thead>

<tbody id="topIpTable">

</tbody>

</table>

</div>

<div class="bg-white p-6 rounded shadow mt-6">

<h2 class="font-bold mb-4">
Top Countries
</h2>

<table class="w-full">

<thead class="border-b">
<tr>
<th class="p-2 text-left">Country</th>
<th class="p-2 text-left">Visitors</th>
</tr>
</thead>

<tbody id="countryTable">

</tbody>

</table>

</div>

<div class="bg-white p-6 rounded shadow mt-6">

<h2 class="font-bold mb-4">
Latest Visitors
</h2>
</div>

<div class="bg-white p-6 rounded shadow mt-6">

<h2 class="font-bold mb-4">
Live Attack Map
</h2>

<div id="attackMap" style="height:400px;"></div>

</div>

<table class="w-full">

<thead class="border-b">
<tr>
<th class="p-2 text-left">IP</th>
<th class="p-2 text-left">Country</th>
<th class="p-2 text-left">Type</th>
<th class="p-2 text-left">Threat</th>
<th class="p-2 text-left">Time</th>
</tr>
</thead>

<tbody>

@foreach($latestVisitors as $v)

<tr class="border-b">

<td class="p-2">
{{ $v->ip }}
</td>

<td class="p-2">
{{ $v->country }}
</td>

<td class="p-2">

@if($v->is_bot)
<span class="text-red-500">Bot</span>
@else
<span class="text-green-500">Human</span>
@endif

</td>

<td class="p-2">

@if($v->threat == 'CRITICAL')
<span class="text-red-700 font-bold">CRITICAL</span>

@elseif($v->threat == 'HIGH')
<span class="text-red-500">HIGH</span>

@elseif($v->threat == 'MEDIUM')
<span class="text-yellow-500">MEDIUM</span>

@else
<span class="text-green-500">LOW</span>
@endif

</td>

<td class="p-2">
{{ $v->created_at }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

<div class="bg-white p-6 rounded shadow mt-6">

<h2 class="font-bold mb-4">
Live Security Events
</h2>

<table class="w-full">

<thead class="border-b">
<tr>
<th class="p-2 text-left">IP</th>
<th class="p-2 text-left">Country</th>
<th class="p-2 text-left">Threat</th>
<th class="p-2 text-left">Time</th>
</tr>
</thead>

<tbody id="securityEvents">

</tbody>

</table>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

<script>

var options = {
    chart: {
        type: 'line',
        height: 350
    },

    series: [
        { name: 'Human', data: [] },
        { name: 'Bot', data: [] }
    ],

    xaxis: {
        categories: []
    },

    colors: ['#22c55e','#ef4444']
};

var chart = new ApexCharts(document.querySelector("#trafficChart"), options);

chart.render();

function loadTraffic(){

fetch('/api/traffic-stats')
.then(res=>res.json())
.then(data=>{

let time = new Date().toLocaleTimeString();

options.series[0].data.push(data.human);
options.series[1].data.push(data.bot);

options.xaxis.categories.push(time);

chart.updateOptions(options);

});

}

setInterval(loadTraffic,5000);


// TOP IP
function loadTopIp(){

fetch('/api/top-ip')
.then(res=>res.json())
.then(data=>{

let html = '';

data.forEach(row=>{

html += `
<tr class="border-b">
<td class="p-2">${row.ip}</td>
<td class="p-2 text-red-500 font-bold">${row.total}</td>
</tr>
`;

});

document.getElementById('topIpTable').innerHTML = html;

});

}

loadTopIp();
setInterval(loadTopIp,10000);

function loadCountries(){

fetch('/api/country-stats')
.then(res=>res.json())
.then(data=>{

let html = '';

data.forEach(row=>{

html += `
<tr class="border-b">
<td class="p-2">🌍 ${row.country}</td>
<td class="p-2 font-bold">${row.total}</td>
</tr>
`;

});

document.getElementById('countryTable').innerHTML = html;

});

}

loadCountries();

setInterval(loadCountries,10000);

// ATTACK MAP

var map = new jsVectorMap({
selector: "#attackMap",
map: "world",
zoomButtons: true,

regionStyle: {
initial: {
fill: "#e5e7eb"
}
}

});

function loadAttackMap(){

fetch('/api/attack-map')
.then(res=>res.json())
.then(data=>{

let regions = {};

data.forEach(row=>{

if(row.country == "Vietnam") regions["VN"] = row.total;
if(row.country == "United States") regions["US"] = row.total;
if(row.country == "China") regions["CN"] = row.total;
if(row.country == "Singapore") regions["SG"] = row.total;

});

map.updateSeries([
{
attribute: "fill",
values: regions
}
]);

});

}

loadAttackMap();

setInterval(loadAttackMap,10000);

// SECURITY EVENTS

function loadSecurityEvents(){

fetch('/api/security-events')
.then(res=>res.json())
.then(data=>{

let html = '';

data.forEach(row=>{

html += `
<tr class="border-b">
<td class="p-2">${row.ip}</td>
<td class="p-2">${row.country}</td>
<td class="p-2 text-red-500 font-bold">${row.threat}</td>
<td class="p-2">${row.created_at}</td>
</tr>
`;

});

document.getElementById('securityEvents').innerHTML = html;

});

}

loadSecurityEvents();
setInterval(loadSecurityEvents,5000);

</script>

@endsection
