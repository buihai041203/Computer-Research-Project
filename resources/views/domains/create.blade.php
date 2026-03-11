@extends('layouts.panel')

@section('content')

<h1 class="text-2xl font-bold mb-6">
Add Domain
</h1>

<form method="POST" action="/domains">

@csrf

<div class="mb-4">

<label>Domain</label>

<input type="text"
name="domain"
class="border p-2 w-full">

</div>

<div class="mb-4">

<label>IP Address</label>

<input type="text"
name="ip"
class="border p-2 w-full">

</div>

<button class="bg-green-500 text-white px-4 py-2 rounded">



Save

</button>

</form>

@endsection
