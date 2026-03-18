
<x-guest-layout>
    <style>
        /* Tùy chỉnh riêng cho Form để đồng bộ dự án 5M */
        .auth-card {
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 20px !important;
            padding: 2.5rem !important;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5) !important;
        }
        .auth-input {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(56, 189, 248, 0.2) !important;
            color: white !important;
            border-radius: 10px !important;
        }
        .auth-input:focus {
            border-color: #38bdf8 !important;
            ring-color: #38bdf8 !important;
            outline: none;
        }
        label { color: #94a3b8 !important; font-size: 12px !important; text-transform: uppercase; font-weight: bold; }
        
    </style>

    <div class="auth-card">
        <div class="mb-6 text-center">
            <h1 class="page-title" style="font-size: 1.5rem !important;">SECURE ACCESS</h1>
            <p class="text-xs text-dim mt-1">Authorized Personnel Only</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email">Email Terminal</label>
                <input id="email" class="auth-input block mt-1 w-full p-2.5" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <label for="password">Security Key</label>
                <input id="password" class="auth-input block mt-1 w-full p-2.5" type="password" name="password" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-700 bg-slate-900 text-blue-500 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="ms-2 text-sm text-slate-400">{{ __('Stay Authenticated') }}</span>
                </label>
            </div>

            <div class="flex flex-col gap-4 mt-6">
                <button type="submit" class="btn-add w-full justify-center py-3">
                    {{ __('INITIALIZE SESSION') }}
                </button>

                <div class="flex justify-between items-center">
                    @if (Route::has('password.request'))
                        <a class="text-xs text-slate-500 hover:text-blue-400 transition" href="{{ route('password.request') }}">
                            {{ __('Lost credentials?') }}
                        </a>
                    @endif
                    
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>