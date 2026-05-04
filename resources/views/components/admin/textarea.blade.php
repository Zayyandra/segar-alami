@props([
    'label',
    'name',
    'value' => null,
    'rows' => 4,
    'required' => false,
    'placeholder' => null,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-1.5">
        {{ $label }}@if ($required) <span class="text-red-500">*</span>@endif
    </label>
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($required) required @endif
        class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 {{ $errors->has($name) ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
