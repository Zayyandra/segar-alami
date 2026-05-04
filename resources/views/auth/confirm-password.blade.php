<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Password — Segar Alami</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f0f2f7;
            background-image: radial-gradient(circle, #c9cdd8 1px, transparent 1px);
            background-size: 22px 22px;
        }
        .input-field {
            width: 100%;
            border-radius: 0.75rem;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #1e293b;
            transition: all 0.15s;
            outline: none;
        }
        .input-field::placeholder { color: #cbd5e1; }
        .input-field:focus {
            background: #fff;
            border-color: transparent;
            box-shadow: 0 0 0 2.5px #10b981;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 font-sans antialiased">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-300/40 border border-slate-100 px-10 pt-10 pb-9">

            <div class="flex flex-col items-center mb-8">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z" />
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Konfirmasi Password</h1>
                <p class="text-sm text-slate-400 mt-1.5 text-center leading-relaxed">
                    Area ini memerlukan verifikasi ulang.<br>Masukkan password kamu untuk melanjutkan.
                </p>
                <div class="w-full h-px bg-slate-100 mt-6"></div>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Password
                    </label>
                    <input type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••" class="input-field">
                </div>

                <button type="submit"
                    class="w-full mt-2 rounded-2xl bg-emerald-500 hover:bg-emerald-600
                           active:scale-[0.98] text-white font-bold py-3.5 text-sm tracking-wide
                           transition-all duration-150 shadow-lg shadow-emerald-200">
                    Konfirmasi &amp; Lanjutkan
                </button>
            </form>
        </div>

        <p class="mt-6 text-sm text-slate-400 text-center">
            <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 transition">
                ← Kembali ke Login
            </a>
        </p>
    </div>
</body>
</html>
