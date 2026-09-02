@props([
    'icon' => 'bi-inbox',
    'title' => 'Aucun résultat',
    'text' => null,
    'small' => false,
])

<div {{ $attributes->merge(['class' => 'tk-empty '.($small ? 'tk-empty--sm' : '')]) }}>
    <div class="tk-empty__icon"><i class="bi {{ $icon }}"></i></div>
    <div class="tk-empty__title">{{ $title }}</div>
    @if ($text)
        <p class="tk-empty__text mb-0">{{ $text }}</p>
    @endif

    @if (! $slot->isEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>
