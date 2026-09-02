@props([
    'title' => null,
    'flush' => false,
])

<section {{ $attributes->merge(['class' => 'tk-card']) }}>
    @if ($title || isset($actions))
        <div class="tk-card__head">
            <h2 class="tk-card__title">{{ $title }}</h2>
            @isset($actions)
                <div class="d-flex align-items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="tk-card__body {{ $flush ? 'tk-card__body--flush' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="tk-card__foot">{{ $footer }}</div>
    @endisset
</section>
