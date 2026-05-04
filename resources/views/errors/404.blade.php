<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center px-4 font-sans antialiased">

    <div class="w-full max-w-lg text-center">

        {{-- Logo --}}
        <div class="flex justify-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center overflow-hidden p-1.5">
                <img src="{{ asset('images/logo-segar-alami.jpeg') }}" alt="Segar Alami"
                    class="w-full h-full object-contain">
            </div>
        </div>

        {{-- Angka besar --}}
        <div class="relative mb-6">
            <p class="text-[120px] font-extrabold text-slate-200 leading-none select-none">404</p>
            <div class="absolute inset-0 flex items-center justify-center">
                <div
                    class="w-16 h-16 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">Halaman Tidak Ditemukan</h1>
        <p class="text-sm text-slate-500 mb-8 max-w-sm mx-auto">
            Halaman yang Anda cari tidak ada, sudah dipindahkan, atau URL-nya salah.
        </p>

        <div class="flex items-center justify-center gap-3">
            <a href="javascript:history.back()"
                class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700
                      text-sm font-medium hover:bg-slate-50 transition">
                ← Kembali
            </a>
            <a href="{{ auth()->check() ? route('app.dashboard') : route('login') }}"
                class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600
          text-white text-sm font-medium transition shadow-md shadow-emerald-100">
                {{ auth()->check() ? 'Ke Dashboard' : 'Ke Login' }}
            </a>
        </div>

        <p class="mt-8 text-xs text-slate-400">Segar Alami · Sistem Informasi UMKM</p>
    </div>

</body>

</html>
