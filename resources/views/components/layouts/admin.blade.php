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
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">

        {{-- Overlay gelap saat drawer terbuka (mobile saja) --}}
        <div x-show="sidebarOpen" x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"
            style="display:none;"></div>

        <x-admin.sidebar />

        <div class="flex-1 flex flex-col min-w-0">
            <x-admin.topbar :title="$title ?? 'Dashboard'" :subtitle="$subtitle ?? null" :expiredCount="$expiredCount" />
            {{-- Banner notifikasi expired --}}
            @if (($expiredCount ?? 0) > 0)
                <div class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                    <div
                        class="rounded-xl bg-red-50 border border-red-200 px-4 sm:px-5 py-3 flex items-center justify-between gap-3">
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

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
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

                    @if (session('warning'))
                        <div
                            class="mb-4 rounded-lg bg-amber-50 border border-amber-100 px-4 py-3 text-amber-700 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            {{ session('warning') }}
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

    {{-- Modal Konfirmasi Global --}}
    <div id="confirm-modal"
        style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; align-items:center; justify-content:center; padding:16px;">
        <div id="confirm-modal-backdrop"
            style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.5);"></div>
        <div
            style="position:relative; background:#ffffff; border-radius:16px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); max-width:384px; width:100%; padding:24px; z-index:10000;">
            <div style="display:flex; align-items:flex-start; gap:16px;">
                <div
                    style="width:40px; height:40px; border-radius:9999px; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg style="width:20px; height:20px; color:#dc2626;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div style="flex:1;">
                    <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin:0 0 4px 0;">Konfirmasi</h3>
                    <p id="confirm-modal-message" style="font-size:14px; color:#475569; margin:0;"></p>
                </div>
            </div>
            <div style="margin-top:24px; display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                <button type="button" id="confirm-modal-cancel"
                    style="padding:8px 16px; border-radius:8px; font-size:14px; font-weight:500; color:#475569; background:transparent; border:none; cursor:pointer;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                    Batal
                </button>
                <button type="button" id="confirm-modal-ok"
                    style="padding:8px 20px; border-radius:8px; background:#dc2626; color:#ffffff; font-size:14px; font-weight:500; border:none; cursor:pointer;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script>
        window.__modalSiap = true;
        console.log('[modal] script konfirmasi dimuat ✓');

        (function() {
            const modal = document.getElementById('confirm-modal');
            const msgEl = document.getElementById('confirm-modal-message');
            const btnOk = document.getElementById('confirm-modal-ok');
            const btnCancel = document.getElementById('confirm-modal-cancel');
            const backdrop = document.getElementById('confirm-modal-backdrop');
            let targetForm = null;

            function tutup() {
                modal.style.display = 'none';
                targetForm = null;
            }

            // Tangkap KLIK tombol submit di dalam form[data-confirm] — lebih andal daripada event submit
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('button[type="submit"], input[type="submit"]');
                if (!btn) return;

                const form = btn.closest('form[data-confirm]');
                if (!form) return;

                console.log('[modal] tombol hapus diklik:', form.dataset.confirm);
                e.preventDefault();
                targetForm = form;
                msgEl.textContent = form.dataset.confirm;
                modal.style.display = 'flex';
            });

            btnOk.addEventListener('click', function() {
                if (!targetForm) return;
                const f = targetForm;
                tutup();
                f.submit();
            });

            btnCancel.addEventListener('click', tutup);
            backdrop.addEventListener('click', tutup);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') tutup();
            });
        })();
    </script>
</body>

</html>
