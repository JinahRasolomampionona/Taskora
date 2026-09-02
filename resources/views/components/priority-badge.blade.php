@props(['priority'])

@php
    $priority = $priority instanceof \App\Enums\TaskPriority
        ? $priority
        : \App\Enums\TaskPriority::from($priority);
@endphp

<span {{ $attributes->merge(['class' => 'tk-badge '.$priority->badgeClass()]) }}>
    @if ($priority === \App\Enums\TaskPriority::Urgent)
        <i class="bi bi-exclamation-triangle-fill"></i>
    @endif
    {{ $priority->label() }}
</span>
