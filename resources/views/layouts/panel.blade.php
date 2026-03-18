<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>{{ request()->server('SERVER_ADDR') ?? '127.0.0.1' }}</title>

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
    flex:1;
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

/* ===== LOGOUT ===== */

/* ===== LOGOUT ===== */

.logout-item {
    margin: 1rem; /* Tạo khoảng cách với menu phía trên */
    border-top: 1px solid rgba(255,255,255,0.1); /* Đường kẻ mờ phân cách */
    padding-top: 1.5rem !important;
}

.logout-item form {
    padding: 12px 14px;
    border-radius: 10px;
    transition: all .25s ease;
    cursor: pointer;
    border: 1px solid rgba(248, 113, 113, 0.2); /* Viền đỏ mờ cho nút Logout */
}

.logout-btn {
    width: 100%;
    text-align: center;
    background: none;
    border: none;
    color: #f87171;
    font-weight: 600;
    cursor: pointer;
    display: block;
}

/* HIỆU ỨNG GIỐNG MENU KHÁC */
.logout-item form:hover {
    background: rgba(248, 113, 113, 0.1); /* Nền đỏ cực mờ khi hover */
    transform: translateX(6px); /* Hiệu ứng di chuyển sang phải */
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    border-color: #f87171;
}

.logout-item form:hover .logout-btn {
    color: #ff8a8a; /* Chữ sáng hơn khi hover */
}

.logout-item form:active {
    transform: scale(0.96);
}

/* ===== CONTENT AREA ===== */

.flex-1{
    margin-left:260px;
}

/* ===== PAGE CONTENT ===== */

.p-6{
    margin-top:20px;
}

/* ===== RESPONSIVE ===== */

@media (max-width:900px){

.w-64{
    width:200px;
}

.flex-1{
    margin-left:200px;
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

<!-- Logout moved here -->

<div class="sidebar-footer logout-item">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            Logout
        </button>
    </form>
</div>

</div>

<!-- Content -->

<div class="flex-1 flex flex-col">

<div class="p-6">

@yield('content')

</div>

</div>

</div>

</body>
</html>