@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-amber']) }}>
    {{ $value ?? $slot }}
</label>
