<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>5M Security - Intelligent Monitoring</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,700|inter:400,700,800" rel="stylesheet" />

    <style>
        :root {
            --bg-dark: #020617;
            --accent-blue: #38bdf8;
            --accent-green: #10b981;
            --panel-bg: rgba(15, 23, 42, 0.7);
            --border-glass: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--bg-dark);
            /* Nền lưới Grid đặc trưng của dự án */
            background-image: 
                linear-gradient(rgba(56, 189, 248, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            font-family: 'Inter', sans-serif;
            color: #f8fafc;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Tiêu đề 5M trắng xanh giống Dashboard */
        .brand-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -2px;
            background: linear-gradient(90deg, #ffffff 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 15px rgba(56, 189, 248, 0.4));
        }

        .glass-card {
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 3rem;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-out;
        }

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-blue), #2563eb);
            color: white;
            padding: 12px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            margin: 10px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(56, 189, 248, 0.3);
            filter: brightness(1.1);
        }

        .btn-outline {
            display: inline-block;
            background: transparent;
            color: #f8fafc;
            padding: 12px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid var(--border-glass);
            transition: all 0.3s;
            margin: 10px;
        }

        .btn-outline:hover {
            background: rgba(255,255,255,0.05);
            border-color: var(--accent-blue);
        }

        .live-status {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: var(--accent-green);
            margin-top: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .pulse {
            width: 8px;
            height: 8px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 10px var(--accent-green); }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scanline effect */
        body::after {
            content: " ";
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.02), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.02));
            background-size: 100% 4px, 3px 100%;
            pointer-events: none;
            z-index: 100;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <h1 class="brand-title">5M SECURITY</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; margin: 1.5rem 0 2rem 0;">
                Next-generation Intelligent Web Server Panel. <br>
                Monitor, Protect, and Scale with Confidence.
            </p>

            <div class="flex">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">Go to Console</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Access Panel</a>
                        
                    @endauth
                @endif
            </div>

            <div class="live-status">
                <div class="pulse"></div>
                SYSTEM STATUS: ACTIVE // ENCRYPTION: AES-256
            </div>
        </div>
    </div>

    <footer style="padding: 20px; text-align: center; color: #475569; font-size: 0.8rem; position: relative; z-index: 10;">
        &copy; {{ date('Y') }} 5M Smart Webserver Panel. All rights reserved.
    </footer>
</body>
</html>