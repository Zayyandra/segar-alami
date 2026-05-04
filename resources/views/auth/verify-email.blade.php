<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email — Segar Alami</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f0f2f7;
            background-image: radial-gradient(circle, #c9cdd8 1px, transparent 1px);
            background-size: 22px 22px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 font-sans antialiased">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-300/40 border border-slate-100 px-10 pt-10 pb-9">

            <div class="flex flex-col items-center mb-8">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Verifikasi Email</h1>
                <p class="text-sm text-slate-400 mt-1.5 text-center leading-relaxed">
                    Cek inbox kamu. Kami sudah mengirimkan link<br>verifikasi ke alamat email kamu.
                </p>
                <div class="w-full h-px bg-slate-100 mt-6"></div>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm text-emerald-700 flex items-start gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                    </svg>
                    Link verifikasi baru telah dikirim ke email kamu.
                </div>
            @endif

            <div class="rounded-2xl bg-slate-50 border border-slate-100 px-5 py-4 mb-6">
                <p class="text-sm text-slate-500 leading-relaxed">
                    Tidak menerima email? Periksa folder <span class="font-semibold text-slate-700">Spam</span> atau klik tombol di bawah untuk kirim ulang.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full rounded-2xl bg-emerald-500 hover:bg-emerald-600
                           active:scale-[0.98] text-white font-bold py-3.5 text-sm tracking-wide
                           transition-all duration-150 shadow-lg shadow-emerald-200">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                           active:scale-[0.98] text-slate-500 font-semibold py-3.5 text-sm
                           transition-all duration-150">
                    Keluar
                </button>
            </form>
        </div>

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
