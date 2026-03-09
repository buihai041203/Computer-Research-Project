@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
Traffic Logs
</h1>

<table class="w-full bg-white shadow rounded">

<thead class="bg-gray-100">
<tr>
<th class="p-3">IP</th>
<th class="p-3">Country</th>
<th class="p-3">Bot</th>
<th class="p-3">Time</th>
</tr>
</thead>

<tbody>

@foreach($visitors as $v)

<tr class="border-t">
<td class="p-3">{{ $v->ip }}</td>
<td class="p-3">{{ $v->country }}</td>
<td class="p-3">
@if($v->is_bot)
Bot
@else
Human
@endif
</td>
<td class="p-3">{{ $v->created_at }}</td>
</tr>

@endforeach

</tbody>

</table>

@endsection
