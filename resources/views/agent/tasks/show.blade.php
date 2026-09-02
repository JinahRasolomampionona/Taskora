@extends('layouts.app')

@section('title', $task->code.' · '.$task->title)

@section('content')
    <x-page-header :title="$task->title"
                   :breadcrumbs="[
                       'Taskora' => route('agent.dashboard'),
                       'Mes tâches' => route('agent.tasks.index'),
                       $task->code => null,
                   ]">
        <x-status-actions :task="$task" :transitions="$transitions" label="Faire évoluer" />
    </x-page-header>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="tk-table__code">{{ $task->code }}</span>
        <x-status-badge :status="$task->status" :task="$task" />
        <x-priority-badge :priority="$task->priority" />
        @if ($task->isOverdue())
            <span class="tk-badge tk-badge-overdue"><i class="bi bi-clock-history"></i>En retard</span>
        @endif
        @if ($task->project)
            <span class="tk-badge tk-badge-neutral">
                <span class="tk-dot" style="background:{{ $task->project->color }}"></span>
                {{ $task->project->name }}
            </span>
        @endif
    </div>

    <div class="row g-3">
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

        <div class="col-lg-4">
            <x-panel title="Informations">
                <div class="tk-meta-list">
                    <div>
                        <div class="tk-meta-item__label">Client</div>
                        <x-user-chip :user="$task->client" :sub="$task->client?->company" size="sm" />
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Responsable</div>
                        <x-user-chip :user="$task->agent" size="sm" placeholder="Non assignée" />
                    </div>

                    <div>
                        <div class="tk-meta-item__label">Projet</div>
                        <div class="tk-meta-item__value">{{ $task->project?->name ?? 'Non rattachée' }}</div>
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
                </div>
            </x-panel>
        </div>
    </div>
@endsection
