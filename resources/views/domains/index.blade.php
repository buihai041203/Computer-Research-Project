@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
Domain Manager
</h1>

<!-- ADD DOMAIN BUTTON -->

<div class="mb-6">

<a href="/domains/create"
class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">

+ Add Domain

</a>

</div>

<!-- DOMAIN LIST -->

<div class="bg-white p-6 rounded shadow">

@if(session('success'))
<div class="bg-green-100 text-green-700 p-3 rounded mb-4">
{{ session('success') }}
</div>
@endif

<table class="w-full">

<thead class="border-b bg-gray-50">

<tr>

<th class="p-3 text-left">Domain</th>

<th class="p-3 text-left">IP Address</th>

<th class="p-3 text-left">Status</th>

<th class="p-3 text-left">Created</th>

<th class="p-3 text-left">Action</th>

</tr>

</thead>

<tbody>

@forelse($domains as $domain)

<tr class="border-b hover:bg-gray-50">

<td class="p-3 font-semibold">

{{ $domain->domain }}

</td>

<td class="p-3">

{{ $domain->ip }}

</td>

<td class="p-3">

@if($domain->status == 'active')

<span class="text-green-600 font-semibold">
Active
</span>

@else

<span class="text-yellow-600 font-semibold">
Pending
</span>

@endif

</td>

<td class="p-3 text-gray-500">

{{ $domain->created_at }}

</td>

<td class="p-3">

<form method="POST" action="/domains/{{ $domain->id }}" 
onsubmit="return confirm('Delete this domain?')">

@csrf
@method('DELETE')

<button class="text-red-500 hover:underline">

Delete

</button>

</form>

</td>

</tr>

@empty

<tr>

<td colspan="5" class="p-4 text-center text-gray-500">

No domains found.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

@endsection