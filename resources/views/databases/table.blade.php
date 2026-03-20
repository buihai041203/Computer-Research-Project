@extends('layouts.panel')

@section('content')
<h2>{{ $cfg->site_name }} / {{ $table }}</h2>
<a href="{{ route('databases.show', $cfg->site_name) }}">← Back</a>

<table border="1" cellpadding="6">
<tr>
@foreach($columns as $c)
<th>{{ $c->Field }}</th>
@endforeach
</tr>

@foreach($rows as $r)
<tr>
@foreach((array)$r as $v)
<td>{{ is_scalar($v) ? $v : json_encode($v) }}</td>
@endforeach
</tr>
@endforeach
</table>
@endsection
