@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div class="tk-page-head">
    <div class="min-w-0">
        @if (count($breadcrumbs))
            <nav class="tk-breadcrumb" aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    @foreach ($breadcrumbs as $label => $url)
                        @if ($loop->last || $url === null)
                            <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        @endif

        <h1 class="tk-page-title">{{ $title }}</h1>

        @if ($subtitle)
            <p class="tk-page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="d-flex flex-wrap align-items-center gap-2">{{ $slot }}</div>
    @endif
</div>
