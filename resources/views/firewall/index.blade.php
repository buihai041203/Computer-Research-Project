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
</style>

<h1 class="text-2xl font-bold mb-6">
    Firewall Panel
</h1>

<div class="bg-white p-6 rounded shadow mb-6">

    <form method="POST" action="/firewall/block">
        @csrf

        <div class="flex gap-4">

            <input name="ip"
                class="border p-2 rounded w-1/3"
                placeholder="IP address">

            <input name="reason"
                class="border p-2 rounded w-1/3"
                placeholder="Reason">

            <button class="bg-red-500 text-white px-4 py-2 rounded">
                Block IP
            </button>

        </div>

    </form>

</div>

<div class="bg-white p-6 rounded shadow">

    <table class="w-full">

        <thead class="border-b">

            <tr>
                <th class="p-2 text-left">IP</th>
                <th class="p-2 text-left">Reason</th>
                <th class="p-2 text-left">Action</th>
            </tr>

        </thead>

        <tbody>

            @foreach($ips as $ip)

            <tr class="border-b">

                <td class="p-2">
                    {{ $ip->ip }}
                </td>

                <td class="p-2">
                    {{ $ip->reason }}
                </td>

                <td class="p-2">

                    <form method="POST" action="/firewall/{{ $ip->id }}">
                        @csrf
                        @method('DELETE')

                        <button class="text-green-500">
                            Unblock
                        </button>

                    </form>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection