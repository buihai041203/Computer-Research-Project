@extends('layouts.panel')

@section('content')
<div style="padding:20px;">
<h2>Website: {{ $domainModel->domain }}</h2>

@if(session('success'))
<div style="color:lime; margin-bottom:10px;">{{ session('success') }}</div>
@endif

@if(session('error'))
<div style="color:red; margin-bottom:10px;">{{ session('error') }}</div>
@endif

<h3>DB Config</h3>
<form method="POST" action="{{ route('databases.config', $domainModel->domain) }}">
@csrf

<input name="db_host" value="{{ old('db_host', $cfg->db_host ?? '127.0.0.1') }}" placeholder="db_host">
<input name="db_port" value="{{ old('db_port', $cfg->db_port ?? 3306) }}" placeholder="db_port">
<input name="db_name" value="{{ old('db_name', $cfg->db_name ?? '') }}" placeholder="db_name">
<input name="db_user" value="{{ old('db_user', $cfg->db_user ?? '') }}" placeholder="db_user">
<input type="password" name="db_password" placeholder="db_password (để trống nếu giữ nguyên)">

<label style="margin-left:10px;">
<input type="checkbox" name="is_active" value="1" {{ ($cfg->is_active ?? true) ? 'checked' : '' }}>
Active
</label>

<button type="submit">Save Config</button>
</form>

<hr style="margin:20px 0;">

<h3>Tables</h3>
@if(empty($tables))
<p>Chưa có bảng hoặc chưa cấu hình DB.</p>
@else
<ul>
@foreach($tables as $t)
<li>
<a href="{{ route('databases.table', [$domainModel->domain, $t]) }}">
{{ $t }}
</a>
</li>
@endforeach
</ul>
@endif

<hr style="margin:20px 0;">

<h3>SQL Query (safe mode)</h3>
<form method="POST" action="{{ route('databases.query', $domainModel->domain) }}">
@csrf
<textarea name="sql" rows="6" cols="100" placeholder="SELECT * FROM users LIMIT 10;">{{ old('sql') }}</textarea>
<br>
<button type="submit">Run</button>
</form>

@if(session('query_result'))
<h4 style="margin-top:20px;">Query Result</h4>
<pre>{{ json_encode(session('query_result'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
@endif
</div>
@endsection
