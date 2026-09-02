@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <x-page-header :title="$project->name"
                   :subtitle="$project->description"
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Projets' => route('admin.projects.index'),
                       $project->code => null,
                   ]">
        <a href="{{ route('admin.tasks.index', ['project' => $project->id]) }}" class="btn btn-light btn-sm">
            <i class="bi bi-list-task me-1"></i>Toutes les tâches
        </a>
        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-8">
            <x-panel title="Tâches du projet" flush>
                @if ($tasks->isEmpty())
                    <x-empty-state icon="bi-inbox" title="Aucune tâche rattachée"
                                   text="Rattachez des tâches à ce projet depuis leur formulaire." />
                @else
                    <div class="tk-table-wrap">
                        <table class="table tk-table">
                            <thead>
                                <tr>
                                    <th style="width:100px">Code</th>
                                    <th>Titre</th>
                                    <th style="width:130px">Statut</th>
                                    <th style="width:110px">Priorité</th>
                                    <th style="width:180px">Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td><span class="tk-table__code">{{ $task->code }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.tasks.show', $task) }}"
                                               class="tk-table__link">{{ $task->title }}</a>
                                        </td>
                                        <td><x-status-badge :status="$task->status" /></td>
                                        <td><x-priority-badge :priority="$task->priority" /></td>
                                        <td><x-user-chip :user="$task->agent" size="sm" :sub="false" placeholder="Non assignée" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tk-pagination-bar">
                        <span>{{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} sur {{ $tasks->total() }}</span>
                        {{ $tasks->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-panel>
        </div>

        <div class="col-lg-4">
            <x-panel title="Informations" class="mb-3">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <x-avatar :name="$project->initials" :color="$project->color" size="xl" />
                    <div class="min-w-0">
                        <div class="fw-semibold">{{ $project->name }}</div>
                        <div class="tk-fs-xs tk-text-faint">{{ $project->code }}</div>
                    </div>
                </div>

                <div class="tk-meta-list">
                    <div>
                        <div class="tk-meta-item__label">Propriétaire</div>
                        <x-user-chip :user="$project->owner" size="sm" placeholder="Non défini" />
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="tk-meta-item__label">Tâches</div>
                            <div class="tk-meta-item__value tk-tabular">{{ $project->tasks_count }}</div>
                        </div>
                        <div class="col-6">
                            <div class="tk-meta-item__label">En cours</div>
                            <div class="tk-meta-item__value tk-tabular">{{ $project->open_tasks_count }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">État</div>
                        <div>
                            @if ($project->is_active)
                                <span class="tk-badge tk-badge-status-completed">Actif</span>
                            @else
                                <span class="tk-badge tk-badge-status-archived">Archivé</span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-panel>

            <x-panel title="Équipe">
                @if ($project->members->isEmpty())
                    <x-empty-state icon="bi-people" title="Aucun membre" small />
                @else
                    <div class="d-grid gap-3">
                        @foreach ($project->members as $member)
                            <x-user-chip :user="$member" size="sm" />
                        @endforeach
                    </div>
                @endif
            </x-panel>
        </div>
    </div>
@endsection
