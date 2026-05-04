<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password — Segar Alami</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25z" />
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Buat Password Baru</h1>
                <p class="text-sm text-slate-400 mt-1.5 text-center leading-relaxed">
                    Masukkan password baru kamu di bawah ini.<br>Minimal 8 karakter.
                </p>
                <div class="w-full h-px bg-slate-100 mt-6"></div>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required
                        autocomplete="username" class="input-field" placeholder="admin@segaralami.com">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Password Baru
                    </label>
                    <input type="password" name="password" required autocomplete="new-password"
                        placeholder="••••••••" class="input-field">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                        placeholder="••••••••" class="input-field">
                </div>

                <button type="submit"
                    class="w-full mt-2 rounded-2xl bg-emerald-500 hover:bg-emerald-600
                           active:scale-[0.98] text-white font-bold py-3.5 text-sm tracking-wide
                           transition-all duration-150 shadow-lg shadow-emerald-200">
                    Reset Password
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
