@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
Security Events
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
