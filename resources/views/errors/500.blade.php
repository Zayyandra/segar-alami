<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Server Error</title>
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
            <p class="text-[120px] font-extrabold text-amber-100 leading-none select-none">500</p>
            <div class="absolute inset-0 flex items-center justify-center">
                <div
                    class="w-16 h-16 rounded-full bg-amber-50 border-2 border-amber-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">Terjadi Kesalahan Server</h1>
        <p class="text-sm text-slate-500 mb-8 max-w-sm mx-auto">
            Server mengalami masalah saat memproses permintaan Anda.
            Coba refresh halaman atau kembali ke dashboard.
        </p>

        <div class="flex items-center justify-center gap-3">
            <a href="javascript:location.reload()"
                class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700
                      text-sm font-medium hover:bg-slate-50 transition">
                ↺ Refresh
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
