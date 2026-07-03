@php
    $user = auth()->user();
    $isOwner = $user->hasRole('owner');
    $isAdmin = $user->hasRole('admin');

    $initials = collect(explode(' ', $user->name))
        ->take(2)
        ->map(fn($w) => strtoupper($w[0]))
        ->join('');
    $roleName = $user->getRoleNames()->first() ?? 'user';

    $expiredCount = 0;
    if ($isAdmin) {
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

    if ($isAdmin) {
        $groups = [
            [
                'label' => null,
                'items' => [
                    ['route' => 'app.dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
                    ['route' => 'app.penjualan.index', 'label' => 'Penjualan', 'icon' => 'receipt'],
                ],
            ],
            [
                'label' => 'Katalog',
                'items' => [
                    ['route' => 'app.kategori.index', 'label' => 'Kategori', 'icon' => 'tag'],
                    ['route' => 'app.produk.index', 'label' => 'Produk', 'icon' => 'bag'],
                    ['route' => 'app.varian-produk.index', 'label' => 'Varian Produk', 'icon' => 'layers'],
                    [
                        'route' => 'app.konversi-produk.index',
                        'label' => 'Konversi Produk',
                        'icon' => 'arrows-right-left',
                    ],
                ],
            ],
            [
                'label' => 'Inventaris',
                'items' => [
                    ['route' => 'app.bahan-baku.index', 'label' => 'Bahan Baku', 'icon' => 'beaker'],
                    ['route' => 'app.bahan-masuk.index', 'label' => 'Bahan Masuk', 'icon' => 'inbox'],
                    ['route' => 'app.bahan-keluar.index', 'label' => 'Bahan Keluar', 'icon' => 'outbox'],
                ],
            ],
            [
                'label' => 'Analitik',
                'items' => [['route' => 'app.masa-simpan.index', 'label' => 'Masa Simpan', 'icon' => 'clock']],
            ],
        ];
    } else {
        $groups = [
            [
                'label' => null,
                'items' => [['route' => 'app.dashboard', 'label' => 'Dashboard', 'icon' => 'grid']],
            ],
            [
                'label' => 'Laporan',
                'items' => [
                    ['route' => 'app.laporan.penjualan', 'label' => 'Laporan Penjualan', 'icon' => 'chart'],
                    ['route' => 'app.laporan.persediaan', 'label' => 'Laporan Persediaan', 'icon' => 'clipboard'],
                ],
            ],
            [
                'label' => 'Manajemen',
                'items' => [['route' => 'app.users.index', 'label' => 'Manajemen Pengguna', 'icon' => 'users']],
            ],
        ];
    }
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col shrink-0 min-h-screen
           transform transition-transform duration-200 ease-in-out
           lg:static lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    style="background-color: #1a2035;">

    {{-- Tombol tutup (mobile saja) --}}
    <button type="button" @click="sidebarOpen = false"
        class="lg:hidden absolute top-4 right-4 w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    {{-- Logo --}}
    <div class="px-4 py-6 flex justify-center">
        <div class="w-24 h-24 rounded-2xl bg-white flex items-center justify-center overflow-hidden p-2 shadow-sm">
            <img src="{{ asset('images/logo-segar-alami.jpeg') }}" alt="Logo Segar Alami"
                class="w-full h-full object-contain">
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 space-y-4 overflow-y-auto pb-4">
        @foreach ($groups as $group)
            <div>
                @if ($group['label'])
                    <p class="px-4 mb-1 text-[10px] font-bold uppercase tracking-widest"
                        style="color: rgba(255,255,255,0.25);">
                        {{ $group['label'] }}
                    </p>
                @endif
                <div class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php
                            $exists = \Route::has($item['route']);
                            $pattern =
                                $item['route'] === 'app.dashboard'
                                    ? 'app.dashboard'
                                    : (str_ends_with($item['route'], '.penjualan') ||
                                    str_ends_with($item['route'], '.persediaan')
                                        ? $item['route']
                                        : rtrim(preg_replace('/\.index$/', '', $item['route']), '.') . '.*');
                            $active = $exists && request()->routeIs($pattern);
                            $href = $exists ? route($item['route']) : '#';
                        @endphp
                        <a href="{{ $href }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                                  {{ $active ? 'text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }}"
                            @if ($active) style="background: rgba(16,185,129,0.12);" @endif>
                            <x-admin.icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
                            <span class="flex-1">{{ $item['label'] }}</span>
                            @if ($item['route'] === 'app.bahan-masuk.index' && $expiredCount > 0)
                                <span
                                    class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold leading-none">
                                    {{ $expiredCount > 9 ? '9+' : $expiredCount }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- User + Logout --}}
    <div class="p-3" style="border-top: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-xl" style="background: rgba(255,255,255,0.04);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                style="background: rgba(16,185,129,0.2); color: #34d399;">
                {{ $initials }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                <p class="text-xs capitalize" style="color: #34d399;">{{ $roleName }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium
                       text-slate-400 hover:text-white hover:bg-white/5 transition-all duration-150">
                <x-admin.icon name="logout" class="w-5 h-5 shrink-0" />
                <span>Keluar</span>
            </button>
        </form>
    </div>

</aside>
