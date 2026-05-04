@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])

@php
    $base = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2';
    $variants = [
        'primary'   => 'bg-primary-500 text-white hover:bg-primary-600 focus:ring-primary-500',
        'secondary' => 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 focus:ring-slate-300',
        'danger'    => 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
