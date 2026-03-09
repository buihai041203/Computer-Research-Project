@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
System Logs
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
