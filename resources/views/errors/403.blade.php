<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center px-4 font-sans antialiased">

    <div class="w-full max-w-lg text-center">

        <div class="flex justify-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center overflow-hidden p-1.5">
                <img src="{{ asset('images/logo-segar-alami.jpeg') }}" alt="Segar Alami"
                    class="w-full h-full object-contain">
            </div>
        </div>

        <div class="relative mb-6">
            <p class="text-[120px] font-extrabold text-red-100 leading-none select-none">403</p>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-red-50 border-2 border-red-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">Akses Ditolak</h1>
        <p class="text-sm text-slate-500 mb-8 max-w-sm mx-auto">
            Anda tidak memiliki izin untuk mengakses halaman ini.
            Hubungi owner jika Anda merasa ini adalah kesalahan.
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
