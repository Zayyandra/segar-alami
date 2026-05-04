@props(['title' => null, 'subtitle' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 overflow-hidden']) }}>
    @if ($title || $action)
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                @if ($title)<h2 class="text-lg font-semibold text-slate-900">{{ $title }}</h2>@endif
                @if ($subtitle)<p class="text-sm text-slate-500 mt-0.5">{{ $subtitle }}</p>@endif
            </div>
            @if ($action)<div>{{ $action }}</div>@endif
        </div>
    @endif
    <div>{{ $slot }}</div>
</div>
