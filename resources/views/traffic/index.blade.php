@extends('layouts.panel')

@section('content')
<style>

body {
    background-color: #020617 !important;
    background-image: 
        linear-gradient(rgba(56, 189, 248, 0.03) 1px, transparent 1px),
        linear-gradient(90px, rgba(56, 189, 248, 0.03) 1px, transparent 1px);
    background-size: 30px 30px;
    font-family: 'Inter', sans-serif;
    color: #e2e8f0;
}


.page-title {
    font-size: 2rem !important;
    font-weight: 800 !important;
    text-transform: uppercase;
    background: linear-gradient(90deg, #ffffff 0%, #0284c7 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 1px;
}


.glass-panel {
    background: rgba(15, 23, 42, 0.8) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
}


table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px; 

thead th {
    color: #38bdf8; 
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 10px 20px;
    text-align: left;
    border: none;
}

tbody td {
    background: rgba(255, 255, 255, 0.03); 
    padding: 16px 20px;
    font-size: 0.9rem;
    color: #cbd5e1;
    border: none !important;
}


tbody td:first-child { border-radius: 12px 0 0 12px; }
tbody td:last-child { border-radius: 0 12px 12px 0; }


tbody tr:hover td {
    background: rgba(56, 189, 248, 0.1) !important;
    color: #fff;
    transition: all 0.3s ease;
}


.badge {
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-block;
}

.badge-bot {
    background: rgba(244, 63, 94, 0.15);
    color: #f43f5e;
    border: 1px solid rgba(244, 63, 94, 0.4);
}

.badge-human {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.4);
}

.font-mono-tech {
    font-family: 'JetBrains Mono', monospace;
    color: #38bdf8;
}
</style>

<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="page-title">Traffic Monitoring</h1>
        <p class="text-gray-400 text-sm mt-1">Analyzing incoming web requests in real-time</p>
    </div>
    <div class="flex items-center gap-2 text-xs text-green-400 font-bold uppercase tracking-widest">
        <span class="live-indicator" style="width:8px; height:8px; background:#10b981; border-radius:50%; display:inline-block; box-shadow: 0 0 8px #10b981; animation: pulse 1.5s infinite;"></span>
        Live Stream
    </div>
</div>

<div class="glass-panel">
    <table>
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Target Domain</th>
                <th>Entity Type</th>
                <th>Request Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td class="font-mono-tech">{{ $log->ip }}</td>
                <td>
                    <span class="opacity-70 mr-1">🌐</span> {{ $log->domain }}
                </td>
                <td>
                    @if($log->type == 'bot')
                        <span class="badge badge-bot"> BOT</span>
                    @else
                        <span class="badge badge-human"> HUMAN</span>
                    @endif
                </td>
                <td class="text-gray-400 text-xs">
                    {{ $log->created_at->diffForHumans() }}
                </td>
            </tr>
            @endforeach
            
            @if($logs->isEmpty())
            <tr>
                <td colspan="4" class="text-center py-10 opacity-50 italic">No traffic data detected yet.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

@keyframes pulse {
    0% { transform: scale(0.9); opacity: 0.7; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(0.9); opacity: 0.7; }
}
@endsection