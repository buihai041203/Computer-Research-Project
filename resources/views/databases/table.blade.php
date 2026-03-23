@extends('layouts.panel')

@section('content')
<style>
:root {
    --surface-0: #050a14;
    --surface-1: #0a1220;
    --surface-2: #161f32;
    --border-faint: rgba(148, 163, 184, 0.1);
    --cyan: #22d3ee;
    --green: #4ade80;
    --red: #f87171;
    --blue: #0ea5e9;
    --text-primary: #e2e8f0;
    --text-secondary: #94a3b8;
    --font-ui: 'DM Sans', sans-serif;
    --font-mono: 'Space Mono', monospace;
}

body {
    background: var(--surface-0);
    color: var(--text-primary);
    font-family: var(--font-ui);
}

/* Container & Header */
div[style*="padding:20px"] {
    max-width: 1400px; /* Rộng hơn cho trang Browse */
    margin: 0 auto;
    padding: 30px 20px !important;
}

h2 {
    font-size: 1.5rem;
    border-bottom: 1px solid var(--border-faint);
    padding-bottom: 15px;
    margin-bottom: 20px;
    font-family: var(--font-mono);
}

/* Navigation Buttons */
div[style*="display:flex; gap:12px"] a, 
div[style*="display:flex; gap:12px"] span {
    font-family: var(--font-mono);
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 700;
    border-width: 1px !important;
}

a[href*="databases.show"] { color: var(--text-secondary) !important; border-color: var(--border-faint) !important; }
span[style*="color:#16a34a"] { background: rgba(74, 222, 128, 0.1); border-color: var(--green) !important; }
a[href*="structure"] { color: var(--blue) !important; border-color: var(--blue) !important; }

/* Alerts */
div[style*="color:lime"] { color: var(--green) !important; font-family: var(--font-mono); font-size: 13px; }
div[style*="color:red"] { color: var(--red) !important; font-family: var(--font-mono); font-size: 13px; }

/* Table Styling */
div[style*="overflow:auto"] {
    background: var(--surface-1);
    border: 1px solid var(--border-faint);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

table {
    border-collapse: collapse;
    border: none !important;
    width: 100%;
}

table thead th {
    background: rgba(255,255,255,0.03);
    color: var(--cyan);
    font-family: var(--font-mono);
    font-size: 11px;
    text-transform: uppercase;
    padding: 12px !important;
    border: none !important;
    border-bottom: 2px solid var(--border-faint) !important;
    text-align: left;
}

table tbody td {
    padding: 8px !important;
    border: none !important;
    border-bottom: 1px solid var(--border-faint) !important;
    font-family: var(--font-mono);
    font-size: 13px;
    color: var(--text-secondary);
}

table tbody tr:hover {
    background: rgba(34, 211, 238, 0.02);
}

/* Form Inputs inside Table */
input[name^="row"] {
    background: transparent;
    border: 1px solid transparent;
    color: var(--text-primary);
    padding: 6px 10px;
    border-radius: 4px;
    width: 100%;
    transition: 0.2s;
    font-family: var(--font-mono);
}

input[name^="row"]:focus {
    background: var(--surface-2);
    border-color: var(--cyan);
    outline: none;
    box-shadow: 0 0 8px rgba(34, 211, 238, 0.2);
}

/* Save Button */
button[type="submit"] {
    background: rgba(34, 211, 238, 0.1);
    border: 1px solid var(--cyan);
    color: var(--cyan);
    padding: 4px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-family: var(--font-mono);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    transition: 0.3s;
}

button[type="submit"]:hover {
    background: var(--cyan);
    color: #000;
}

/* Primary Key info */
p {
    font-size: 13px;
    color: var(--text-secondary);
    background: var(--surface-1);
    display: inline-block;
    padding: 6px 15px;
    border-radius: 20px;
    border: 1px solid var(--border-faint);
}

strong { color: var(--cyan); }
</style>
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
