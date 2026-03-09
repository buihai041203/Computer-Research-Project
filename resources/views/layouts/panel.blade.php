<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Smart Security Panel</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-gray-100">

<div class="flex h-screen">

    <!-- Sidebar -->

    <div class="w-64 bg-white shadow">

        <div class="p-4 text-xl font-bold border-b">
            SmartPanel
        </div>

        <ul class="p-4 space-y-2">

            <li class="p-2 hover:bg-gray-100 rounded">
                <a href="/dashboard">Dashboard</a>
            </li>

            <li class="p-2 hover:bg-gray-100 rounded">
                <a href="/domains">Domains</a>
            </li>

            <li class="p-2 hover:bg-gray-100 rounded">
                <a href="/traffic">Traffic</a>
            </li>

            <li class="p-2 hover:bg-gray-100 rounded">
                <a href="/security">Security</a>
            </li>

            <li class="p-2 hover:bg-gray-100 rounded">
                <a href="/logs">Logs</a>
            </li>

            <li class="p-2 hover:bg-gray-100 rounded">
                <a href="/firewall">Firewall</a>
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

</body>
</html>
