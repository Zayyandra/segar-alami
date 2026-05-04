@php
    $expiredCount = 0;
    if (auth()->check() && auth()->user()->hasRole('admin')) {
        $expiredCount = \App\Models\BahanMasuk::whereNotNull('tanggal_kadaluarsa')
            ->where(function ($q) {
                $q->whereDate('tanggal_kadaluarsa', '<', today())->orWhere(function ($q2) {
                    $q2->whereDate('tanggal_kadaluarsa', '>=', today())->whereDate(
                        'tanggal_kadaluarsa',
                        '<=',
                        today()->addDays(7),
                    );
                });
            })
            ->count();
    }
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Segar Alami</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-segar-alami.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">
        <x-admin.sidebar />
        <div class="flex-1 flex flex-col min-w-0">
            <x-admin.topbar :title="$title ?? 'Dashboard'" :subtitle="$subtitle ?? null" :expiredCount="$expiredCount" />
            {{-- Banner notifikasi expired --}}
            @if (($expiredCount ?? 0) > 0)
                <div class="mx-6 lg:mx-8 mt-4">
                    <div
                        class="rounded-xl bg-red-50 border border-red-200 px-5 py-3 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm text-red-700 font-medium">
                                {{ $expiredCount }} bahan baku mendekati atau telah melewati masa simpan.
                            </p>
                        </div>
                        <a href="{{ route('app.bahan-masuk.index') }}"
                            class="text-xs font-semibold text-red-600 hover:text-red-700 whitespace-nowrap underline underline-offset-2">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            @endif

            <main class="flex-1 p-8">
                <div class="max-w-7xl mx-auto">

                    @if (session('success'))
                        <div
                            class="mb-4 rounded-lg bg-emerald-50 border border-emerald-100 px-4 py-3 text-emerald-700 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div
                            class="mb-4 rounded-lg bg-red-50 border border-red-100 px-4 py-3 text-red-700 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 border border-red-100 px-4 py-3 text-red-700 text-sm">
                            <p class="font-semibold mb-1">Terdapat kesalahan input:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>
