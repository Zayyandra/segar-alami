@props(['title', 'subtitle' => null, 'expiredCount' => 0])

@php $user = auth()->user(); @endphp

<header class="bg-slate-100 px-6 lg:px-8 pt-6 pb-4 flex items-start justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="flex items-center gap-3 mt-1">

        @if ($expiredCount > 0)
            <a href="{{ route('app.bahan-masuk.index') }}"
               class="relative flex items-center justify-center w-9 h-9 rounded-full bg-red-50 border border-red-100 hover:bg-red-100 transition"
               title="{{ $expiredCount }} bahan baku perlu diperhatikan">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center leading-none">
                    {{ $expiredCount > 9 ? '9+' : $expiredCount }}
                </span>
            </a>
        @endif

        <div class="text-right">
            <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
            <p class="text-xs text-emerald-600 capitalize">{{ $user->getRoleNames()->first() }}</p>
        </div>
        @php
            $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
        @endphp
        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold"
             style="background: rgba(16,185,129,0.15); color: #059669;">
            {{ $initials }}
        </div>
        <div class="text-xs text-slate-400 border-l border-slate-200 pl-3">
            {{ now()->translatedFormat('d M Y') }}
        </div>
    </div>
</header>
