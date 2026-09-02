@props([
    'user' => null,
    'name' => null,
    'color' => null,
    'size' => null,
])

@php
    $initials = $user?->initials ?? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim((string) $name), 0, 2));
    $background = $color ?? $user?->avatar_color ?? '#94a3b8';
    $sizeClass = $size ? 'tk-avatar-'.$size : '';
    $label = $user?->name ?? $name;
@endphp

<span {{ $attributes->merge([
        'class' => 'tk-avatar '.$sizeClass,
        'style' => 'background:'.$background,
        'title' => $label,
    ]) }}>{{ $initials ?: '?' }}</span>
