<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Segar Alami</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center px-4 font-sans antialiased bg-slate-100"
      style="background-image: radial-gradient(circle, #c9cdd8 1px, transparent 1px); background-size: 22px 22px;">

    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-300/40 border border-slate-100 px-10 pt-10 pb-9">

            {{-- Logo --}}
            <div class="flex flex-col items-center mb-8">
                <div class="w-24 h-24 rounded-2xl bg-white border border-slate-100 shadow-md flex items-center justify-center overflow-hidden p-2 mb-4">
                    <img src="{{ asset('images/logo-segar-alami.jpeg') }}" alt="Segar Alami"
                        class="w-full h-full object-contain">
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Segar Alami</h1>
                <p class="text-[11px] text-slate-400 tracking-widest uppercase mt-0.5">Sistem Informasi UMKM</p>

                <div class="w-full h-px bg-slate-100 mt-6"></div>

                <div class="mt-5 text-center">
                    <h2 class="text-lg font-bold text-slate-700">Selamat Datang</h2>
                    <p class="text-sm text-slate-400 mt-0.5">Masuk ke akun dashboard Anda</p>
                </div>
            </div>

            {{-- Error --}}
            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@segaralami.com"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800
                               placeholder:text-slate-300 transition
                               focus:outline-none focus:bg-white focus:border-transparent focus:ring-[2.5px] focus:ring-emerald-500
                               {{ $errors->has('email') ? 'border-red-300 bg-red-50 focus:ring-red-500' : '' }}"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Kata Sandi
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-16 text-sm text-slate-800
                                   placeholder:text-slate-300 transition
                                   focus:outline-none focus:bg-white focus:border-transparent focus:ring-[2.5px] focus:ring-emerald-500
                                   {{ $errors->has('password') ? 'border-red-300 bg-red-50 focus:ring-red-500' : '' }}"
                        >
                        <button
                            type="button"
                            onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'Lihat':'Sembunyikan'"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400 hover:text-slate-600 transition">
                            Lihat
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2.5">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                    >
                    <label for="remember" class="text-sm text-slate-500 cursor-pointer select-none">
                        Ingat saya
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full rounded-2xl bg-emerald-500 hover:bg-emerald-600 active:scale-[0.98]
                           text-white font-bold py-3.5 text-sm tracking-wide
                           transition-all duration-150 shadow-lg shadow-emerald-200 mt-1">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <p class="mt-6 text-sm text-slate-400 text-center">
            Butuh bantuan?
            <a href="mailto:support@segaralami.com"
               class="font-semibold text-emerald-600 hover:text-emerald-700 transition">
                Hubungi IT Support
            </a>
        </p>
    </div>

</body>
</html>
