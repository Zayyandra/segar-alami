@props(['title', 'subtitle' => null])

<header class="bg-[--color-surface] px-6 lg:px-8 pt-6 pb-2 flex items-start justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <button class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
        </button>
        <div class="text-right text-xs">
            <div class="font-semibold text-primary-700 uppercase tracking-wide">Operational Hub</div>
            <div class="text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</div>
        </div>
    </div>
</header>
