@extends('layouts.panel')

@section('content')
<div style="padding:20px;">
<h2>{{ $domainModel->domain }} / {{ $table }}</h2>
<a href="{{ route('databases.show', $domainModel->domain) }}">← Back</a>
<div style="margin:10px 0 16px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <a href="{{ route('databases.show', $domainModel->domain) }}" style="padding:6px 10px; border:1px solid #ccc; border-radius:6px; text-decoration:none;">← Back to database</a>
    <span style="padding:6px 10px; border:1px solid #16a34a; border-radius:6px; color:#16a34a; font-weight:600;">Browse</span>
    <a href="{{ route('databases.structure', [$domainModel->domain, $table]) }}" style="padding:6px 10px; border:1px solid #0ea5e9; border-radius:6px; text-decoration:none; color:#0ea5e9;">Structure</a>
</div>

@if(session('success'))
<div style="color:lime; margin-top:10px;">{{ session('success') }}</div>
@endif

@if(session('error'))
<div style="color:red; margin-top:10px;">{{ session('error') }}</div>
@endif

<p style="margin-top:10px;">
Primary key:
<strong>{{ $primaryKey ?? 'Không có PK (không edit trực tiếp được)' }}</strong>
</p>

<div style="overflow:auto; margin-top:10px;">
<table border="1" cellpadding="6" cellspacing="0" style="min-width:1000px;">
<thead>
<tr>
@foreach($columns as $c)
<th>{{ $c->Field }}</th>
@endforeach
<th>Action</th>
</tr>
</thead>

<tbody>
@forelse($rows as $r)
@php $row = (array) $r; @endphp

@if($primaryKey && isset($row[$primaryKey]))
<form method="POST" action="{{ route('databases.row.update', [$domainModel->domain, $table, $row[$primaryKey]]) }}">
@csrf
<tr>
@foreach($columns as $c)
@php $field = $c->Field; @endphp
<td>
<input
name="row[{{ $field }}]"
value="{{ is_scalar($row[$field] ?? null) ? $row[$field] : json_encode($row[$field] ?? null) }}"
style="min-width:180px;"
>
</td>
@endforeach
<td>
<button type="submit">Save</button>
</td>
</tr>
</form>
@else
<tr>
@foreach($columns as $c)
@php $field = $c->Field; @endphp
<td>{{ is_scalar($row[$field] ?? null) ? $row[$field] : json_encode($row[$field] ?? null) }}</td>
@endforeach
<td>-</td>
</tr>
@endif

@empty
<tr>
<td colspan="{{ count($columns) + 1 }}" style="text-align:center;">
Không có dữ liệu
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>
@endsection
