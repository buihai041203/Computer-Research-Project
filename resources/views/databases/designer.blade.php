@extends('layouts.panel')

@section('content')
<div style="padding:20px;">
    <h2>{{ $domainModel->domain }} / Database Designer</h2>
    <a href="{{ route('databases.show', $domainModel->domain) }}">← Back</a>

    <h3 style="margin-top:16px;">Tables</h3>
    <ul>
        @foreach($tables as $t)
            <li><a href="{{ route('databases.table', [$domainModel->domain, $t]) }}">{{ $t }}</a></li>
        @endforeach
    </ul>

    <h3 style="margin-top:16px;">Relations (FK)</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
        <thead>
            <tr>
                <th>From Table</th>
                <th>From Column</th>
                <th>To Table</th>
                <th>To Column</th>
            </tr>
        </thead>
        <tbody>
        @forelse($relations as $r)
            <tr>
                <td>{{ $r->TABLE_NAME }}</td>
                <td>{{ $r->COLUMN_NAME }}</td>
                <td>{{ $r->REFERENCED_TABLE_NAME }}</td>
                <td>{{ $r->REFERENCED_COLUMN_NAME }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;">No foreign-key relation found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
