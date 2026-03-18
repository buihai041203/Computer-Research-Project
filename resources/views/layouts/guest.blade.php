<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '5M Security') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg-dark: #020617;
                --accent-blue: #38bdf8;
            }

            body {
                background-color: var(--bg-dark) !important;
                /* NỀN LƯỚI ĐỒNG BỘ VỚI DASHBOARD */
                background-image: 
                    linear-gradient(rgba(56, 189, 248, 0.05) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(56, 189, 248, 0.05) 1px, transparent 1px);
                background-size: 40px 40px;
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            
            .page-title {
                font-size: 2.5rem;
                font-weight: 800;
                text-transform: uppercase;
                background: linear-gradient(90deg, #ffffff 0%, #0284c7 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: 1rem;
                display: inline-block;
            }

            /* Nút bấm đồng bộ style Dashboard */
            .btn-add {
                background: linear-gradient(135deg, var(--accent-blue), #2563eb);
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 10px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .btn-add:hover {
                transform: translateY(-2px);
                box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
            }

            .text-dim { color: #94a3b8; }
            
label, .x-input-label {
    color: #94a3b8 !important; 
}


a {
    color: #e2e8f0 !important; 
    text-decoration: none;
}

a:hover {
    color: #38bdf8 !important; 
    text-decoration: underline !important;
}


.text-gray-600, .text-slate-500, .ms-2 {
    color: #94a3b8 !important;
}
            
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="flex flex-col items-center justify-center w-full p-4">
            <div class="mb-8 text-center">
                <a href="/">
                    <h1 class="page-title">5M SECURITY</h1>
                </a>
                <p class="text-dim text-xs tracking-widest uppercase opacity-70">Access Control Terminal</p>
            </div>

            <div class="w-full sm:max-w-md px-2">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>