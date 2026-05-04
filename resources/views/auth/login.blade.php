<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Segar Alami</title>
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

        .input-field::placeholder {
            color: #cbd5e1;
        }

        .input-field:focus {
            background: #fff;
            border-color: transparent;
            box-shadow: 0 0 0 2.5px #10b981;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 font-sans antialiased">

    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-300/40 border border-slate-100 px-10 pt-10 pb-9">

            {{-- Logo inside card --}}
            <div class="flex flex-col items-center mb-8">
                <div
                    class="w-28 h-28 rounded-2xl bg-white border border-slate-100 shadow-md flex items-center justify-center overflow-hidden p-2 mb-4">
                    <img src="{{ asset('images/logo-segar-alami.jpeg') }}" alt="Segar Alami"
                        class="w-full h-full object-contain">
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Segar Alami</h1>
                <p class="text-[11px] text-slate-400 tracking-widest uppercase mt-0.5">Sistem Informasi UMKM</p>

                <div class="w-full h-px bg-slate-100 mt-6"></div>

                <div class="mt-5 text-center">
                    <h2 class="text-lg font-bold text-slate-700">Welcome Back</h2>
                    <p class="text-sm text-slate-400 mt-0.5">Sign in to your dashboard</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Email or Username
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@segaralami.com"
                        required autofocus autocomplete="email" class="input-field">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                                Forgot?
                            </a>
                        @endif
                    </div>
                    <input type="password" name="password" required autocomplete="current-password" class="input-field">
                </div>

                <div class="flex items-center gap-2.5 pt-1">
                    <input type="checkbox" name="remember" id="remember"
                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <label for="remember" class="text-sm text-slate-500 cursor-pointer select-none">
                        Keep me signed in
                    </label>
                </div>

                <button type="submit"
                    class="w-full mt-2 rounded-2xl bg-emerald-500 hover:bg-emerald-600
                               active:scale-[0.98] text-white font-bold py-3.5 text-sm tracking-wide
                               transition-all duration-150 shadow-lg shadow-emerald-200">
                    Masuk Ke Dashboard
                </button>
            </form>
        </div>

        {{-- Footer outside card --}}
        <p class="mt-6 text-sm text-slate-400 text-center">
            Need assistance?
            <a href="mailto:support@segaralami.com"
                class="font-semibold text-emerald-600 hover:text-emerald-700 transition">
                Contact IT Support
            </a>
        </p>
    </div>

</body>

</html>
