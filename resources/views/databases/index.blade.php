@extends('layouts.panel')

@section('content')
<h1>Databases</h1>

@if(session('success')) <div style="color:lime">{{ session('success') }}</div> @endif
@if(session('error')) <div style="color:red">{{ session('error') }}</div> @endif

<table border="1" cellpadding="8">
<tr>
<th>Site</th><th>DB Name</th><th>Host</th><th>Port</th><th>Status</th><th>Action</th>
</tr>
@foreach($sites as $s)
<tr>
<td>{{ $s->site_name }}</td>
<td>{{ $s->db_name ?? 'chưa cấu hình' }}</td>
<td>{{ $s->db_host }}</td>
<td>{{ $s->db_port }}</td>
<td>{{ $s->is_active ? 'ON' : 'OFF' }}</td>
<td><a href="{{ route('databases.show', $s->site_name) }}">Open</a></td>
</tr>
@endforeach
</table>
@endsection
