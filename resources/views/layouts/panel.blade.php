<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Smart Security Panel</title>

@vite(['resources/css/app.css','resources/js/app.js'])

<style>

/* ===== GLOBAL ===== */

body{
    background:#f1f5f9;
    font-family:"Segoe UI", sans-serif;
    color:#1e293b;
}

/* ===== SIDEBAR ===== */

.w-64{
    position:fixed;
    left:0;
    top:0;
    height:100vh;
    width:260px;

    /* màu sáng hơn */
    background:linear-gradient(180deg,#334155,#1e293b);

    color:white;
    box-shadow:4px 0 25px rgba(0,0,0,0.15);
    padding-top:10px;
}

/* LOGO */

.w-64 .text-xl{
    color:white !important;
    letter-spacing:1px;
}

/* ===== MENU ===== */

.w-64 ul{
    margin-top:10px;
}

.w-64 ul li{
    padding:12px 14px;
    border-radius:10px;
    transition:all .25s ease;
    cursor:pointer;
    position:relative;
}

/* MENU TEXT */

.w-64 a{
    color:#cbd5f5 !important;
    font-weight:500;
    display:block;
    transition:.25s;
}

/* ===== HOVER EFFECT ===== */

.w-64 li:hover{

    background:rgba(255,255,255,0.08);

    transform:translateX(6px);

    box-shadow:0 4px 10px rgba(0,0,0,0.15);
}

.w-64 li:hover a{
    color:white !important;
}

/* ===== CLICK EFFECT ===== */

.w-64 li:active{

    transform:scale(0.96);

    background:rgba(255,255,255,0.15);
}

/* ===== ACTIVE PAGE ===== */

.w-64 li.active{

    background:linear-gradient(135deg,#3b82f6,#2563eb);

    box-shadow:
        0 4px 14px rgba(37,99,235,0.45),
        inset 0 0 10px rgba(255,255,255,0.1);
}

.w-64 li.active a{
    color:white !important;
    font-weight:600;
}

/* ===== CONTENT AREA ===== */

.flex-1{
    margin-left:260px;
}

/* ===== TOPBAR ===== */

.bg-white.shadow.p-4{

    position:fixed;
    top:0;
    left:260px;
    right:0;

    z-index:1000;

    background:white;

    border-bottom:1px solid #e5e7eb;

    box-shadow:0 2px 15px rgba(0,0,0,0.05);
}

/* ===== PAGE CONTENT ===== */

.p-6{
    margin-top:80px;
}

/* ===== BUTTON ===== */

button{
    cursor:pointer;
    font-weight:500;
}

/* ===== RESPONSIVE ===== */

@media (max-width:900px){

.w-64{
    width:200px;
}

.flex-1{
    margin-left:200px;
}

.bg-white.shadow.p-4{
    left:200px;
}

}

</style>

</head>

<body>

<div class="flex h-screen">

<!-- Sidebar -->

<div class="w-64 shadow">

<div class="p-4 text-xl font-bold border-b">
SmartPanel
</div>

<ul class="p-4 space-y-2">

<li class="p-2 {{ request()->is('dashboard') ? 'active' : '' }}">
    <a href="/dashboard"> Dashboard</a>
</li>

<li class="p-2 {{ request()->is('domains') ? 'active' : '' }}">
    <a href="/domains"> Domains</a>
</li>

<li class="p-2 {{ request()->is('traffic') ? 'active' : '' }}">
    <a href="/traffic"> Traffic</a>
</li>
<li class="p-2 {{ request()->is('databases') ? 'active' : '' }}">
    <a href="/databases">Databases</a>
    </a>
</li>

<li class="p-2 {{ request()->is('security') ? 'active' : '' }}">
    <a href="/security"> Security</a>
</li>

<li class="p-2 {{ request()->is('logs') ? 'active' : '' }}">
    <a href="/logs"> Logs</a>
</li>

<li class="p-2 {{ request()->is('firewall') ? 'active' : '' }}">
    <a href="/firewall"> Firewall</a>
</li>

</ul>

</div>

<!-- Content -->

<div class="flex-1 flex flex-col">

<!-- Topbar -->

<div class="bg-white shadow p-4 flex justify-between">

<div>
Admin Panel
</div>

<div>

<form method="POST" action="{{ route('logout') }}">
@csrf
<button class="text-red-500">
Logout
</button>
</form>

</div>

</div>

<!-- Page Content -->

<div class="p-6">

@yield('content')

</div>

</div>

</div>

<!-- AI Chat Assistant (floating widget) -->
<style>
#ai-chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: Inter, sans-serif; }
#ai-chat-toggle { background: #1d4ed8; border: none; color: white; width: 55px; height: 55px; border-radius: 50%; box-shadow: 0 4px 14px rgba(0,0,0,0.3); cursor: pointer; }
#ai-chat-box { width: 320px; max-width: calc(100vw - 40px); box-shadow: 0 6px 24px rgba(0,0,0,0.35); border-radius: 14px; overflow: hidden; background: #0b1124; border: 1px solid rgba(148,163,184,0.25); display: none; }
#ai-chat-header { background: #111827; color: #e5e7eb; padding: 10px 12px; font-size: .85rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; }
#ai-chat-messages { height: 240px; overflow-y: auto; padding: 10px; background:#050811; color:#e2e8f0; font-size: .9rem; }
.ai-chat-message { margin-bottom: 8px; padding: 8px 10px; border-radius: 10px; line-height: 1.35; white-space: pre-wrap; word-break: break-word; }
.ai-user { background: rgba(59,130,246,0.2); color:#fff; text-align:right; }
.ai-bot { background: rgba(15,23,42,0.85); color:#dbeafe; text-align:left; }
#ai-chat-input-wrap { display:flex; border-top:1px solid rgba(148,163,184,0.2); }
#ai-chat-input { flex:1; border:none; background:#0f172a; color:#e5e7eb; padding:10px; outline:none; }
#ai-chat-send { border:none; background:#2563eb; color:#fff; padding: 0 14px; cursor:pointer; }
</style>
<div id="ai-chat-widget">
    <button id="ai-chat-toggle" title="AI Chat">AI</button>
    <div id="ai-chat-box" role="dialog" aria-label="AI Chat Assistant">
        <div id="ai-chat-header">
            <span>AI Assistant</span>
            <button id="ai-chat-close" style="background:transparent;border:none;color:#fff;cursor:pointer;font-weight:700;">×</button>
        </div>
        <div id="ai-chat-messages" aria-live="polite"></div>
        <div id="ai-chat-input-wrap">
            <input id="ai-chat-input" type="text" placeholder="Ask about top ip, blocked ip, security events" aria-label="Message" />
            <button id="ai-chat-send" type="button">Send</button>
        </div>
    </div>
</div>
<script>
(function(){
    const toggle = document.getElementById('ai-chat-toggle');
    const box = document.getElementById('ai-chat-box');
    const closeBtn = document.getElementById('ai-chat-close');
    const send = document.getElementById('ai-chat-send');
    const input = document.getElementById('ai-chat-input');
    const messages = document.getElementById('ai-chat-messages');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    function addMessage(text, from){
        const el = document.createElement('div');
        el.className = 'ai-chat-message ' + (from === 'user' ? 'ai-user':'ai-bot');
        el.textContent = text;
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
    }

    function sendMessage(){
        const value = input.value.trim();
        if (!value) return;

        addMessage(value, 'user');
        input.value = '';

        fetch('/api/ai-chat', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token || ''
            },
            body: JSON.stringify({ message: value })
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.success && data.message) {
                addMessage(data.message, 'bot');
            } else {
                addMessage('Oops, AI reply failed. Please try again.', 'bot');
            }
        })
        .catch(() => {
            addMessage('Network error. Please try again.', 'bot');
        });
    }

    toggle.addEventListener('click', () => {
        box.style.display = box.style.display === 'block' ? 'none' : 'block';
        if (box.style.display === 'block') input.focus();
    });

    closeBtn.addEventListener('click', () => box.style.display = 'none');
    send.addEventListener('click', sendMessage);

    input.addEventListener('keyup', function(e){
        if (e.key === 'Enter') sendMessage();
    });
})();
</script>

</body>
</html>