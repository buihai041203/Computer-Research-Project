@extends('layouts.panel')

@section('content')
<h2>Site: {{ $cfg->site_name }}</h2>

@if(session('success')) <div style="color:lime">{{ session('success') }}</div> @endif
@if(session('error')) <div style="color:red">{{ session('error') }}</div> @endif

<h3>DB Config</h3>
<form method="POST" action="{{ route('databases.config', $cfg->site_name) }}">
@csrf
<input name="db_host" value="{{ $cfg->db_host }}" placeholder="db_host">
<input name="db_port" value="{{ $cfg->db_port }}" placeholder="db_port">
<input name="db_name" value="{{ $cfg->db_name }}" placeholder="db_name">
<input name="db_user" value="{{ $cfg->db_user }}" placeholder="db_user">
<input type="password" name="db_password" placeholder="db_password (để trống nếu giữ nguyên)">
<button type="submit">Save Config</button>
</form>

<h3>Tables</h3>
<ul>
@foreach($tables as $t)
<li><a href="{{ route('databases.table', [$cfg->site_name, $t]) }}">{{ $t }}</a></li>
@endforeach
</ul>

<h3>SQL Query (SELECT/UPDATE/DELETE an toàn)</h3>
<form method="POST" action="{{ route('databases.query', $cfg->site_name) }}">
@csrf
<textarea name="sql" rows="6" cols="100" placeholder="SELECT * FROM users LIMIT 10;"></textarea>
<br>
<button type="submit">Run</button>
</form>
@endsection
