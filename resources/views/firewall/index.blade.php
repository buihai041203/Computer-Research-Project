@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
Firewall Panel
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
