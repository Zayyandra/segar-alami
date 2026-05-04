@props([
    'label',
    'name',
    'options' => [],
    'value' => null,
    'required' => false,
    'placeholder' => '-- Pilih --',
    'optionValue' => 'id',
    'optionLabel' => 'nama',
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-1.5">
        {{ $label }}@if ($required) <span class="text-red-500">*</span>@endif
    </label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 {{ $errors->has($name) ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $option)
            @php
                $val = is_array($option) ? $option[$optionValue] : $option->{$optionValue};
                $lbl = is_array($option) ? $option[$optionLabel] : $option->{$optionLabel};
            @endphp
            <option value="{{ $val }}" @selected(old($name, $value) == $val)>{{ $lbl }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
