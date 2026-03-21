<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Smart Security Panel</title>

@vite(['resources/css/app.css','resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>

/* ===== GLOBAL ===== */

body{
    background:#060c17;
    font-family:'DM Sans', sans-serif;
    color:#e2e8f0;
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
    
    display:flex;
    flex-direction:column;
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

/* ===== LOGOUT SIDEBAR ===== */
.logout-box{
    margin-top:auto;
    padding:20px;
    display:flex;
    justify-content:center;
}

.logout-btn{
    color:#f87171;
    font-weight:600;
    background:none;
    border:none;
    cursor:pointer;
    text-align:center;
}

/* ===== PAGE CONTENT ===== */

.p-6{
    margin-top:0px;
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
<div class="logout-box">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="logout-btn">
            Logout
        </button>
    </form>
</div>
</div>

<!-- Content -->

<div class="flex-1 flex flex-col">


<!-- Page Content -->

<div class="p-6">

@yield('content')

</div>

</div>

</div>

<!-- A<!-- AI Chat Widget — paste trước </body> trong panel.blade.php -->
<style>
#ai-chat-widget * { box-sizing: border-box; }
#ai-chat-widget {
    position: fixed; bottom: 20px; right: 20px; z-index: 9999;
    display: flex; flex-direction: column; align-items: flex-end; gap: 10px;
    font-family: Inter, system-ui, sans-serif;
}
#ai-chat-box {
    width: 320px;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    overflow: hidden;
    display: none;
    flex-direction: column;
}
#ai-chat-header {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    display: flex; align-items: center; justify-content: space-between;
    background: #fff;
}
.aic-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: #EBF4FF;
    display: flex; align-items: center; justify-content: center;
}
.aic-avatar svg { width: 16px; height: 16px; }
.aic-title { font-size: 13px; font-weight: 600; color: #111; line-height: 1.2; }
.aic-status { font-size: 11px; color: #16a34a; display: flex; align-items: center; gap: 4px; }
.aic-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; }
.aic-close {
    width: 26px; height: 26px; border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.08); background: transparent;
    color: #888; cursor: pointer; font-size: 16px; line-height: 1;
    display: flex; align-items: center; justify-content: center;
}
.aic-close:hover { background: #f5f5f5; }
#ai-chat-messages {
    height: 260px; overflow-y: auto; padding: 14px;
    display: flex; flex-direction: column; gap: 6px;
    background: #f8f9fb;
}
#ai-chat-messages::-webkit-scrollbar { width: 3px; }
#ai-chat-messages::-webkit-scrollbar-thumb { background: #ddd; border-radius: 99px; }
.aic-msg {
    max-width: 88%; font-size: 13px; line-height: 1.5;
    padding: 8px 11px; white-space: pre-wrap; word-break: break-word;
    animation: aicIn .15s ease;
}
@keyframes aicIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:translateY(0); } }
.aic-bot {
    align-self: flex-start; background: #fff;
    border: 1px solid rgba(0,0,0,0.07);
    border-radius: 12px 12px 12px 3px; color: #111;
}
.aic-user {
    align-self: flex-end; background: #1d4ed8; color: #fff;
    border-radius: 12px 12px 3px 12px;
}
.aic-time {
    font-size: 10px; color: #aaa; padding: 0 3px;
    align-self: flex-end;
}
.aic-time.l { align-self: flex-start; }
.aic-qr { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 2px; }
.aic-qr button {
    font-size: 11px; padding: 4px 9px; border-radius: 99px;
    border: 1px solid rgba(0,0,0,0.12); background: #fff;
    color: #444; cursor: pointer; transition: all .15s;
}
.aic-qr button:hover { background: #f0f4ff; border-color: #1d4ed8; color: #1d4ed8; }
.aic-typing {
    align-self: flex-start; background: #fff;
    border: 1px solid rgba(0,0,0,0.07);
    border-radius: 12px 12px 12px 3px;
    padding: 10px 14px; display: flex; gap: 4px; align-items: center;
}
.aic-typing span {
    width: 6px; height: 6px; border-radius: 50%; background: #bbb;
    animation: aicBounce 1.2s infinite;
}
.aic-typing span:nth-child(2) { animation-delay:.15s; }
.aic-typing span:nth-child(3) { animation-delay:.3s; }
@keyframes aicBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }
#ai-chat-input-wrap {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px; border-top: 1px solid rgba(0,0,0,0.06);
    background: #fff;
}
#ai-chat-input {
    flex: 1; border: none; background: transparent;
    font-size: 13px; color: #111; outline: none; font-family: inherit;
}
#ai-chat-input::placeholder { color: #aaa; }
#ai-chat-send {
    width: 32px; height: 32px; border-radius: 50%;
    border: none; background: #1d4ed8; color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: transform .1s; flex-shrink: 0;
}
#ai-chat-send:hover { transform: scale(1.05); }
#ai-chat-send:active { transform: scale(0.95); }
#ai-chat-send svg { width: 13px; height: 13px; }
#ai-chat-toggle {
    width: 50px; height: 50px; border-radius: 50%;
    border: none; background: #1d4ed8; color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(29,78,216,0.35);
    transition: transform .15s;
}
#ai-chat-toggle:hover { transform: scale(1.06); }
#ai-chat-toggle svg { width: 22px; height: 22px; }
</style>

<div id="ai-chat-widget">
    <div id="ai-chat-box">
        <div id="ai-chat-header">
            <div style="display:flex;align-items:center;gap:9px">
                <div class="aic-avatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="3"/>
                        <path d="M6 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/>
                        <path d="M19 8c1.1 0 2 .9 2 2s-.9 2-2 2"/>
                        <path d="M5 8c-1.1 0-2 .9-2 2s.9 2 2 2"/>
                    </svg>
                </div>
                <div>
                    <div class="aic-title">AI Security Assistant</div>
                    <div class="aic-status"><span class="aic-dot"></span>Online</div>
                </div>
            </div>
            <button class="aic-close" id="ai-chat-close">×</button>
        </div>
        <div id="ai-chat-messages"></div>
        <div id="ai-chat-input-wrap">
            <input id="ai-chat-input" type="text" placeholder="Ask a question..." maxlength="200" aria-label="Message"/>
            <button id="ai-chat-send" type="button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>
    <button id="ai-chat-toggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
    </button>
</div>

<script>
(function () {
    const box = document.getElementById('ai-chat-box');
    const msgs = document.getElementById('ai-chat-messages');
    const input = document.getElementById('ai-chat-input');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    function getTime() {
        return new Date().toLocaleTimeString('vi', { hour: '2-digit', minute: '2-digit' });
    }

    function addMsg(text, type) {
        const el = document.createElement('div');
        el.className = 'aic-msg aic-' + type;
        el.textContent = text;
        msgs.appendChild(el);
        const t = document.createElement('div');
        t.className = 'aic-time' + (type === 'bot' ? ' l' : '');
        t.textContent = getTime();
        msgs.appendChild(t);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function showTyping() {
        const el = document.createElement('div');
        el.className = 'aic-typing';
        el.id = 'aic-typing';
        el.innerHTML = '<span></span><span></span><span></span>';
        msgs.appendChild(el);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function removeTyping() {
        document.getElementById('aic-typing')?.remove();
    }

    function addQuickReplies() {
        const wrap = document.createElement('div');
        wrap.className = 'aic-qr';
        ['Top IPs today', 'Blocked IPs', 'Any attacks?', 'My domains']
        .forEach(label => {
            const btn = document.createElement('button');
            btn.textContent = label;
            btn.onclick = () => { input.value = label; sendMessage(); };
            wrap.appendChild(btn);
        });
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
    }

    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;
        addMsg(text, 'user');
        input.value = '';
        showTyping();
        try {
            const res = await fetch('/api/ai-chat', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                },
                body: JSON.stringify({ message: text }),
            });
            const data = await res.json();
            removeTyping();
            addMsg('⚠️ No response received.', 'bot');
        } catch {
            removeTyping();
            addMsg('❌ Connection error. Please try again.', 'bot');
        }
    }

    // init greeting
    addMsg('Hello! I can help you check traffic, blocked IPs, security events and more.', 'bot');
    addQuickReplies();

    document.getElementById('ai-chat-toggle').addEventListener('click', () => {
        const isHidden = box.style.display !== 'flex';
        box.style.display = isHidden ? 'flex' : 'none';
        if (isHidden) input.focus();
    });
    document.getElementById('ai-chat-close').addEventListener('click', () => {
        box.style.display = 'none';
    });
    document.getElementById('ai-chat-send').addEventListener('click', sendMessage);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(); });
})();
</script>

</body>
</html>