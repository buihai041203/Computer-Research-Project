@extends('layouts.panel')

@section('content')
<style>
/* ===== MODERN CYBERSECURITY UI VARIABLES ===== */
:root {
    --bg-main: #020617;
    --panel-bg: rgba(15, 23, 42, 0.8);
    --accent-blue: #38bdf8;
    --accent-green: #10b981;
    --accent-red: #f43f5e;
    --accent-yellow: #fbbf24;
    --text-bright: #f8fafc;
    --text-dim: #94a3b8;
    --border-color: rgba(255, 255, 255, 0.1);
}

body {
    background-color: var(--bg-main) !important;
    background-image:
        radial-gradient(circle at 50% -20%, rgba(56,189,248,0.15), transparent),
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
    background-size: 100% 100%, 40px 40px, 40px 40px;
    font-family: 'Inter',"Segoe UI",sans-serif;
    color: var(--text-bright);
}

/* ===== CARDS ===== */
.bg-white{
    background: var(--panel-bg) !important;
    backdrop-filter: blur(12px);
    border: 1px solid var(--border-color) !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4) !important;
    transition: all .3s;
}

.bg-white:hover{
    border-color: var(--accent-blue) !important;
}

/* ===== REGISTER DOMAIN FORM ===== */
form{
    display:flex;
    align-items:center;
    gap:18px;
}

.domain-input{
    flex:1;
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(56,189,248,0.25);
    color:white;
    padding:16px 18px;
    border-radius:12px;
    font-size:14px;
    transition:.25s;
}

.domain-input::placeholder{
    color:#94a3b8;
}

.domain-input:focus{
    border-color: var(--accent-blue);
    background: rgba(255,255,255,0.08);
    box-shadow:0 0 10px rgba(56,189,248,0.25);
    outline:none;
}

.domain-input option {
    background-color: #0f172a; 
    color: white;
}

.btn-add{
    height:50px;
    padding:0 26px;
    background:linear-gradient(135deg,var(--accent-blue),#2563eb);
    border-radius:12px;
    font-weight:700;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:8px;
    transition:.25s;
}

.btn-add:hover{
    transform:translateY(-1px);
    box-shadow:0 0 18px rgba(56,189,248,.35);
}

/* ===== TABLE LAYOUT ===== */
table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 14px;
}

thead th{
    font-size:11px;
    letter-spacing:.15em;
    text-transform:uppercase;
    color:var(--text-dim);
    padding:0 14px 6px 14px;
}

tbody td{
    background:rgba(255,255,255,0.04);
    padding:18px 14px;
    font-size:14px;
    border:none !important;
}

tbody td:first-child{
    border-radius:14px 0 0 14px;
}

tbody td:last-child{
    border-radius:0 14px 14px 0;
}

tbody tr:hover td{
    background:rgba(56,189,248,0.12);
}

/* ===== DOMAIN CELL ===== */
.domain-cell{
    display:flex;
    align-items:center;
    gap:10px;
}

.domain-icon{
    font-size:18px;
}

/* ===== STATUS BADGE ===== */
.badge-status{
    padding:6px 14px;
    border-radius:8px;
    font-size:11px;
    font-weight:700;
    background:rgba(16,185,129,0.15);
    color:var(--accent-green);
    border:1px solid rgba(16,185,129,0.7);
}

.badge-pending {
    padding:6px 14px;
    border-radius:8px;
    font-size:11px;
    font-weight:700;
    background:rgba(251,191,36,0.15);
    color:var(--accent-yellow);
    border:1px solid rgba(251,191,36,0.7);
}

/* ===== ACTION BUTTON ===== */
.action-btn{
    color:#cbd5f5;
    font-size:13px;
    font-weight:600;
    transition:.2s;
}

.action-btn:hover{
    color:var(--accent-blue);
}

/* ===== PAGE TITLE ===== */
.page-title {
    font-size:1.8rem!important;
    font-weight:800!important;
    background: linear-gradient(to right,#fff,var(--accent-blue));
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}
</style>

<div class="mb-8">
    <h1 class="page-title">DOMAIN MANAGEMENT</h1>
    <p class="text-dim text-sm mt-1">Add and monitor your protected web domains</p>
</div>

@if($errors->any())
    <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-xl mb-6">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="bg-white p-8 mb-8">
    <h2 class="text-accent-blue font-bold text-xs uppercase tracking-widest mb-6">
        Register New Domain
    </h2>

    <form method="POST" action="/domains" class="flex gap-4">
        @csrf
        <div class="flex-1">
            <input name="domain"
                class="domain-input w-full p-3 rounded-xl"
                placeholder="e.g. secure-site.com"
                required>
        </div>

        <div class="w-40">
            <select name="php_version" class="domain-input w-full p-3 rounded-xl" required>
                <option value="8.1">PHP 8.1</option>
                <option value="8.2" selected>PHP 8.2</option>
                <option value="8.3">PHP 8.3</option>
            </select>
        </div>

        <button type="submit" class="btn-add text-white rounded-xl font-bold flex items-center gap-2">
            <span>+</span> Add Domain
        </button>
    </form>
</div>

<div class="bg-white p-6">
    <h2 class="text-accent-blue font-bold text-xs uppercase tracking-widest mb-6">Active Subscriptions</h2>
    <table class="w-full">
        <thead>
            <tr>
                <th class="text-left">Domain Name</th>
                <th class="text-left">Root Path</th>
                <th class="text-left">PHP</th>
                <th class="text-left">Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($domains as $domain)
            <tr>
                <td class="font-medium">
                    <span class="text-blue-400">🌐</span> {{ $domain->domain }}
                </td>
                <td class="text-dim font-mono text-xs">
                    {{ $domain->root_path ?? 'N/A' }}
                </td>
                <td class="text-dim text-sm">
                    {{ $domain->php_version ?? 'N/A' }}
                </td>
                <td>
                    @if($domain->status == 'pending_setup')
                        <span class="badge-pending">Pending</span>
                    @else
                        <span class="badge-status">{{ $domain->status ?? 'Active' }}</span>
                    @endif
                </td>
                <td>
                    <div class="flex items-center justify-end gap-4">
                        <button class="text-dim hover:text-blue-400 transition-colors text-xs font-semibold">Settings</button>
                        
                        <form action="/domains/{{ $domain->id }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Xóa domain này sẽ xóa toàn bộ log. Bạn có chắc chắn?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-dim hover:text-red-400 transition-colors text-xs font-semibold">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            
            @if($domains->isEmpty())
            <tr>
                <td colspan="5" class="text-center opacity-50 py-10">No domains registered yet.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection