@extends('layouts.panel')

@section('content')
<div style="padding:20px;">
    <h2>{{ $domainModel->domain }} / {{ $table }} / Structure</h2>
    <a href="{{ route('databases.table', [$domainModel->domain, $table]) }}">← Back to table</a>

    <h3 style="margin-top:16px;">Columns</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
        <thead>
            <tr>
                <th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>
            </tr>
        </thead>
        <tbody>
        @foreach($columns as $c)
            <tr>
                <td>{{ $c->Field }}</td>
                <td>{{ $c->Type }}</td>
                <td>{{ $c->Null }}</td>
                <td>{{ $c->Key }}</td>
                <td>{{ $c->Default }}</td>
                <td>{{ $c->Extra }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3 style="margin-top:16px;">Indexes</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
        <thead>
            <tr>
                <th>Key</th><th>Column</th><th>Unique</th><th>Index Type</th>
            </tr>
        </thead>
        <tbody>
        @foreach($indexes as $i)
            <tr>
                <td>{{ $i->Key_name }}</td>
                <td>{{ $i->Column_name }}</td>
                <td>{{ $i->Non_unique == 0 ? 'YES' : 'NO' }}</td>
                <td>{{ $i->Index_type }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3 style="margin-top:16px;">CREATE TABLE</h3>
    <pre style="white-space:pre-wrap; background:#0b1220; color:#ddd; padding:12px; border-radius:8px;">{{ $ddl }}</pre>
</div>
@endsection
