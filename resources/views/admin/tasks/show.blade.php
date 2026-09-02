@extends('layouts.app')

@section('title', $task->code.' · '.$task->title)

@section('content')
    <x-page-header :title="$task->title"
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Tâches' => route('admin.tasks.index'),
                       $task->code => null,
                   ]">
        <x-status-actions :task="$task" :transitions="$transitions" />

        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-light btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>

        <div class="dropdown">
            <button class="btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false"
                    aria-label="Autres actions">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-header">Assigner à</li>
                @foreach ($agents as $agent)
                    <li>
                        <button type="button" class="dropdown-item"
                                data-tk-assign="{{ $agent->id }}"
                                data-tk-url="{{ route('tasks.assign', $task) }}">
                            {{ $agent->name }}
                            @if ($task->assigned_to === $agent->id)
                                <i class="bi bi-check2 ms-1 text-success"></i>
                            @endif
                        </button>
                    </li>
                @endforeach
                <li>
                    <button type="button" class="dropdown-item text-muted"
                            data-tk-assign="" data-tk-url="{{ route('tasks.assign', $task) }}">
                        Retirer l'assignation
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST"
                          data-tk-confirm="Supprimer définitivement la tâche {{ $task->code }} ?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-trash me-2"></i>Supprimer la tâche
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </x-page-header>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="tk-table__code">{{ $task->code }}</span>
        <x-status-badge :status="$task->status" :task="$task" />
        <x-priority-badge :priority="$task->priority" />
        @if ($task->isOverdue())
            <span class="tk-badge tk-badge-overdue"><i class="bi bi-clock-history"></i>En retard</span>
        @endif
        @if ($task->project)
            <a href="{{ route('admin.projects.show', $task->project) }}" class="tk-badge tk-badge-neutral">
                <span class="tk-dot" style="background:{{ $task->project->color }}"></span>
                {{ $task->project->name }}
            </a>
        @endif
    </div>

    <div class="row g-3">
        {{-- Contenu principal --}}
        <div class="col-lg-8">
            <x-panel title="Description" class="mb-3">
                <div class="tk-prose">{{ $task->description ?: 'Aucune description fournie.' }}</div>

                @if ($task->mission_details)
                    <hr class="my-4">
                    <div class="tk-section-title">Missions à effectuer</div>
                    <div class="tk-prose">{{ $task->mission_details }}</div>
                @endif
            </x-panel>

            @include('tasks.partials.comments', [
                'task' => $task,
                'comments' => $task->comments,
                'allowInternal' => true,
            ])

            @include('tasks.partials.history', ['task' => $task])
        </div>

        {{-- Métadonnées --}}
        <div class="col-lg-4">
            <x-panel title="Informations">
                <div class="tk-meta-list">
                    <div>
                        <div class="tk-meta-item__label">Propriétaire (client)</div>
                        <x-user-chip :user="$task->client" :sub="$task->client?->company" size="sm" />
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Responsable (agent)</div>
                        <div data-tk-agent-slot="{{ $task->id }}">
                            <x-user-chip :user="$task->agent" size="sm" placeholder="Non assignée" />
                        </div>
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Créée par</div>
                        <x-user-chip :user="$task->creator" size="sm" placeholder="Système" />
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Projet</div>
                        <div class="tk-meta-item__value">
                            {{ $task->project?->name ?? 'Non rattachée' }}
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="tk-meta-item__label">Estimation</div>
                            <div class="tk-meta-item__value">{{ $task->estimated_for_humans ?? '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="tk-meta-item__label">Temps enregistré</div>
                            <div class="tk-meta-item__value">{{ $task->spent_for_humans ?? '—' }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Avancement</div>
                        <div class="progress mb-1" role="progressbar"
                             aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100"
                             aria-label="Avancement de la tâche">
                            <div class="progress-bar" style="width: {{ $task->progress }}%"
                                 data-tk-progress="{{ $task->id }}"></div>
                        </div>
                        <div class="tk-fs-xs tk-text-muted">{{ $task->progress }} %</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="tk-meta-item__label">Date limite</div>
                            <div class="tk-meta-item__value {{ $task->isOverdue() ? 'text-danger' : '' }}">
                                {{ $task->due_date?->format('d/m/Y') ?? '—' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="tk-meta-item__label">Créée le</div>
                            <div class="tk-meta-item__value">{{ $task->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    @if ($task->completed_at)
                        <div>
                            <div class="tk-meta-item__label">Terminée le</div>
                            <div class="tk-meta-item__value">{{ $task->completed_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    @endif

                    @if ($task->archived_at)
                        <div>
                            <div class="tk-meta-item__label">Archivée le</div>
                            <div class="tk-meta-item__value">{{ $task->archived_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    @endif
                </div>
            </x-panel>
        </div>
    </div>
@endsection
