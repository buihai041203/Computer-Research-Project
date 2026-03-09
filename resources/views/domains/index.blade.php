@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
Domains
</h1>

<div class="bg-white p-6 rounded shadow mb-6">

<form method="POST" action="/domains">
@csrf

<input name="domain"
class="border p-2 rounded"
placeholder="example.com">

<button class="bg-blue-500 text-white px-4 py-2 rounded">
Add Domain
</button>

</form>

</div>

<div class="bg-white p-6 rounded shadow">

<table class="w-full">

<thead class="border-b">

<tr>
<th class="p-2 text-left">Domain</th>
<th class="p-2 text-left">Status</th>
</tr>

</thead>

<tbody>

@foreach($domains as $domain)

<tr class="border-b">

<td class="p-2">
{{ $domain->domain }}
</td>

<td class="p-2 text-green-500">
{{ $domain->status }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection
