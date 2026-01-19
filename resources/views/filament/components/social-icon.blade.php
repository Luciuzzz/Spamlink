@php
$iconName = $record->icon_preset ?? null;

// Colores oficiales de las redes sociales
$iconColors = [
        'facebook'  => '#1877F2',
        'instagram' => '#E1306C',
        'twitter'   => '#1DA1F2', // X (Twitter)
        'threads'   => '#000000',
        'youtube'   => '#FF0000',
        'tiktok'    => '#000000',
        'mail'     => '#EA4335', // Google/Gmail Red
        'telegram'  => '#24A1DE',
        'whatsapp'  => '#25D366',
        'linkedin'  => '#0A66C2',
        'github'    => '#181717',
        'pinterest' => '#BD081C',
        'website'   => '#4B5563', // Gris neutro (Slate-600)
        'default'   => '#6B7280', // Gris estándar (Gray-500)
    ];
$iconColor = $iconColors[$iconName] ?? 'currentColor';
@endphp

@if ($record->icon_path)
    <img src="{{ asset('storage/'.$record->icon_path) }}" class="w-6 h-6" alt="{{ $record->name }}">
@elseif($iconName)
    <svg data-lucide="{{ $iconName }}" class="w-6 h-6" style="stroke: {{ $iconColor }}"></svg>
@else
    <span class="w-6 h-6 inline-block bg-gray-200 rounded"></span>
@endif
