@extends('layouts.panel')

@section('content')
<style>

.form-card {
    background: rgba(15, 23, 42, 0.8) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 30px;
}

.input-field {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(56, 189, 248, 0.2) !important;
    color: white !important;
    border-radius: 10px;
    padding: 12px;
    width: 100%;
    margin-top: 8px;
}

.input-field:focus {
    border-color: #38bdf8 !important;
    outline: none;
}

label {
    color: #94a3b8;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: bold;
}
</style>

<div class="mb-8">
    <h1 class="page-title">ADD NEW DOMAIN</h1> <p class="text-sm text-dim">Register a new target for monitoring</p>
</div>

<div class="form-card">
    <form method="POST" action="/domains">
        @csrf

        <div class="mb-6">
            <label>Target Domain</label>
            <input type="text" name="domain" class="input-field" placeholder="e.g. example.com">
        </div>

        <div class="mb-6">
            <label>Server IP Address</label>
            <input type="text" name="ip" class="input-field" placeholder="e.g. 192.168.1.1">
        </div>

        <button class="btn-add w-full justify-center py-3">
            Save Configuration
        </button>
    </form>
</div>
@endsection