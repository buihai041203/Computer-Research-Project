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

</body>
</html>
