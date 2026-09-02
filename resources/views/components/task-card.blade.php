@props([
    'task',
    'url' => null,
    'draggable' => false,
    'showStatus' => true,
    'showClient' => true,
    'showAgent' => true,
])

@php
    /** @var \App\Models\Task $task */
    $overdue = $task->isOverdue();
    $estimated = $task->estimated_for_humans;
@endphp

<a href="{{ $url ?? '#' }}"
   data-tk-task-card="{{ $task->id }}"
   data-status="{{ $task->status->value }}"
   data-tk-url="{{ route('tasks.status', $task) }}"
   {{ $attributes->merge([
        'class' => 'tk-task-card tk-reveal'
            .($task->isUrgent() ? ' is-urgent' : '')
            .($draggable ? ' is-draggable' : ''),
   ]) }}>

    <div class="d-flex align-items-start justify-content-between gap-2">
        <span class="tk-task-card__code">{{ $task->code }}</span>

        @if ($showStatus)
            <x-status-badge :status="$task->status" :task="$task" />
        @endif
    </div>

    <div class="tk-task-card__title">{{ $task->title }}</div>

    <div class="tk-task-card__meta">
        <x-priority-badge :priority="$task->priority" />

        @if ($task->relationLoaded('project') && $task->project)
            <span class="tk-badge tk-badge-neutral">
                <span class="tk-dot" style="background:{{ $task->project->color }}"></span>
                {{ $task->project->code }}
            </span>
        @endif

        @if ($overdue)
            <span class="tk-badge tk-badge-overdue">
                <i class="bi bi-clock-history"></i>En retard
            </span>
        @endif
    </div>

    <div class="tk-task-card__foot">
        <div class="d-flex align-items-center gap-1 min-w-0">
            @if ($showAgent)
                @if ($task->relationLoaded('agent') && $task->agent)
                    <x-avatar :user="$task->agent" size="sm" />
                @else
                    <span class="tk-avatar tk-avatar-sm tk-avatar-empty" title="Non assignée">
                        <i class="bi bi-person"></i>
                    </span>
                @endif
            @endif

            @if ($showClient && $task->relationLoaded('client') && $task->client)
                <x-avatar :user="$task->client" size="sm" />
            @endif

            @if ($task->due_date)
                <span class="tk-task-card__time ms-1 {{ $overdue ? 'is-late' : '' }}">
                    <i class="bi bi-calendar3"></i>{{ $task->due_date->format('d/m') }}
                </span>
            @endif
        </div>

        @if ($estimated)
            <span class="tk-task-card__time">
                <i class="bi bi-stopwatch"></i>{{ $estimated }}
            </span>
        @endif
    </div>
</a>
