@props([
    'status',
    'task' => null,
    'icon' => false,
])

@php
    $status = $status instanceof \App\Enums\TaskStatus
        ? $status
        : \App\Enums\TaskStatus::from($status);
@endphp

<span {{ $attributes->merge(['class' => 'tk-badge '.$status->badgeClass()]) }}
      @if ($task) data-tk-status-badge="{{ $task->id }}" @endif>
    @if ($icon)
        <i class="bi bi-{{ $status->icon() }}"></i>
    @endif
    {{ $status->label() }}
</span>
