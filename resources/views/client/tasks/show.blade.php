@extends('layouts.app')

@section('title', $task->code.' · '.$task->title)

@section('content')
    <x-page-header :title="$task->title"
                   :breadcrumbs="[
                       'Taskora' => route('client.dashboard'),
                       'Mes demandes' => route('client.tasks.index'),
                       $task->code => null,
                   ]">
        @can('update', $task)
            <a href="{{ route('client.tasks.edit', $task) }}" class="btn btn-light btn-sm">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
        @endcan

        @if (! empty($transitions))
            <x-status-actions :task="$task" :transitions="$transitions" label="Action" />
        @endif
    </x-page-header>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="tk-table__code">{{ $task->code }}</span>
        <x-status-badge :status="$task->status" :task="$task" />
        <x-priority-badge :priority="$task->priority" />
        @if ($task->project)
            <span class="tk-badge tk-badge-neutral">
                <span class="tk-dot" style="background:{{ $task->project->color }}"></span>
                {{ $task->project->name }}
            </span>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <x-panel title="Votre demande" class="mb-3">
                <div class="tk-prose">{{ $task->description }}</div>
            </x-panel>

            @include('tasks.partials.comments', [
                'task' => $task,
                'comments' => $comments,
                'allowInternal' => false,
            ])

            @include('tasks.partials.history', ['task' => $task])
        </div>

        <div class="col-lg-4">
            <x-panel title="Suivi">
                <div class="tk-meta-list">
                    <div>
                        <div class="tk-meta-item__label">Avancement</div>
                        <div class="progress mb-1" role="progressbar"
                             aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100"
                             aria-label="Avancement de la demande">
                            <div class="progress-bar" style="width: {{ $task->progress }}%"
                                 data-tk-progress="{{ $task->id }}"></div>
                        </div>
                        <div class="tk-fs-xs tk-text-muted">{{ $task->progress }} %</div>
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Interlocuteur</div>
                        <x-user-chip :user="$task->agent" size="sm" placeholder="En attente d'affectation" />
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Projet</div>
                        <div class="tk-meta-item__value">{{ $task->project?->name ?? 'Non rattachée' }}</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="tk-meta-item__label">Déposée le</div>
                            <div class="tk-meta-item__value">{{ $task->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="tk-meta-item__label">Date souhaitée</div>
                            <div class="tk-meta-item__value">{{ $task->due_date?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                    </div>

                    @if ($task->completed_at)
                        <div>
                            <div class="tk-meta-item__label">Terminée le</div>
                            <div class="tk-meta-item__value">{{ $task->completed_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    @endif
                </div>
            </x-panel>
        </div>
    </div>
@endsection
