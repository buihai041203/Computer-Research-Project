@extends('layouts.panel')
@section('content')


<h1 class="text-2xl font-bold mb-6">

Traffic Monitoring

</h1>

<table class="w-full bg-white shadow">

<thead>

<tr class="border-b">

<th class="p-3">IP</th>
<th class="p-3">Domain</th>
<th class="p-3">Type</th>
<th class="p-3">Time</th>

</tr>

</thead>

<tbody>

@foreach($logs as $log)

<tr class="border-b">

<td class="p-3">{{ $log->ip }}</td>

<td class="p-3">{{ $log->domain }}</td>

<td class="p-3">

@if($log->type=='bot')

<span class="text-red-500">BOT</span>

@else

<span class="text-green-500">HUMAN</span>

@endif

</td>

<td class="p-3">{{ $log->created_at }}</td>

</tr>

@endforeach

</tbody>

</table>

@endsection
