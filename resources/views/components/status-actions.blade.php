@props([
    'task',
    'transitions' => [],
    'label' => 'Changer le statut',
    'size' => 'sm',
])

{{--
    Menu des transitions autorisées.
    Les options viennent de TaskWorkflow : la vue n'invente aucune règle.
--}}
<div {{ $attributes->merge(['class' => 'dropdown']) }}>
    <button type="button"
            class="btn btn-primary btn-{{ $size }} dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            @disabled(empty($transitions))>
        <i class="bi bi-arrow-repeat me-1"></i>{{ $label }}
    </button>

    <ul class="dropdown-menu dropdown-menu-end"
        data-tk-transitions="{{ $task->id }}"
        data-tk-url="{{ route('tasks.status', $task) }}">
        @forelse ($transitions as $transition)
            <li>
                <button type="button" class="dropdown-item"
                        data-tk-status="{{ $transition['value'] }}"
                        data-tk-url="{{ route('tasks.status', $task) }}"
                        data-tk-task="{{ $task->id }}"
                        @if (in_array($transition['value'], ['cancelled', 'archived'], true))
                            data-tk-confirm="Confirmer le passage au statut « {{ $transition['label'] }} » ?"
                        @endif>
                    {{ $transition['label'] }}
                </button>
            </li>
        @empty
            <li>
                <span class="dropdown-item-text tk-fs-xs tk-text-muted">Aucune action disponible</span>
            </li>
        @endforelse
    </ul>
</div>
