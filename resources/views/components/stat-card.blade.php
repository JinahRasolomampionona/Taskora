@props([
    'label',
    'value',
    'icon' => 'bi-bar-chart',
    'tone' => null,
    'hint' => null,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
    $toneClass = $tone ? 'tk-stat--'.$tone : '';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'tk-stat '.$toneClass]) }}>
    <div class="tk-stat__head">
        <span class="tk-stat__label">{{ $label }}</span>
        <span class="tk-stat__icon"><i class="bi {{ $icon }}"></i></span>
    </div>
    <div class="tk-stat__value tk-tabular">{{ $value }}</div>
    @if ($hint)
        <div class="tk-stat__hint">{{ $hint }}</div>
    @endif
</{{ $tag }}>
