@extends('layouts.app')

@section('title', 'Projets')

@section('content')
    <x-page-header title="Projets"
                   subtitle="Regroupez les tâches par équipe ou par périmètre client."
                   :breadcrumbs="['Taskora' => route('admin.dashboard'), 'Projets' => null]">
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nouveau projet
        </a>
    </x-page-header>

    <x-panel flush>
        <div class="p-3 border-bottom">
            <form method="GET" action="{{ route('admin.projects.index') }}"
                  class="d-flex flex-wrap align-items-center gap-2">
                <div class="position-relative flex-grow-1" style="max-width:340px">
                    <i class="bi bi-search position-absolute tk-text-faint"
                       style="left:.7rem; top:50%; transform:translateY(-50%); font-size:.85rem;"></i>
                    <input type="search" name="q" value="{{ $filters['q'] }}"
                           class="form-control form-control-sm ps-4"
                           placeholder="Rechercher un projet ou un code…"
                           aria-label="Rechercher un projet">
                </div>

                <select name="active" class="form-select form-select-sm" style="width:auto"
                        aria-label="Filtrer par état">
                    <option value="">Tous les états</option>
                    <option value="1" @selected($filters['active'] === '1')>Actifs</option>
                    <option value="0" @selected($filters['active'] === '0')>Archivés</option>
                </select>

                <button type="submit" class="btn btn-light btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filtrer
                </button>

                @if ($filters['q'] || $filters['active'] !== '')
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>

        @if ($projects->isEmpty())
            <x-empty-state icon="bi-folder2-open" title="Aucun projet"
                           text="Créez un premier projet pour organiser vos tâches par équipe.">
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">Créer un projet</a>
            </x-empty-state>
        @else
            <div class="tk-table-wrap">
                <table class="table tk-table">
                    <thead>
                        <tr>
                            <th style="width:110px">Code</th>
                            <th>Nom du projet</th>
                            <th>Propriétaire</th>
                            <th class="text-center" style="width:110px">Tâches</th>
                            <th class="text-center" style="width:110px">Membres</th>
                            <th style="width:100px">État</th>
                            <th class="text-end" style="width:140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-2">
                                        <span class="tk-dot" style="background:{{ $project->color }}"></span>
                                        <span class="tk-table__code">{{ $project->code }}</span>
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.projects.show', $project) }}"
                                       class="tk-table__link">{{ $project->name }}</a>
                                    @if ($project->description)
                                        <div class="tk-fs-xs tk-text-muted tk-truncate" style="max-width:340px">
                                            {{ $project->description }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <x-user-chip :user="$project->owner" size="sm" placeholder="Non défini" :sub="false" />
                                </td>
                                <td class="text-center tk-tabular">
                                    {{ $project->tasks_count }}
                                    @if ($project->open_tasks_count > 0)
                                        <span class="tk-fs-xs tk-text-muted">({{ $project->open_tasks_count }} en cours)</span>
                                    @endif
                                </td>
                                <td class="text-center tk-tabular">{{ $project->members_count }}</td>
                                <td>
                                    @if ($project->is_active)
                                        <span class="tk-badge tk-badge-status-completed">Actif</span>
                                    @else
                                        <span class="tk-badge tk-badge-status-archived">Archivé</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.tasks.create') }}"
                                           class="tk-icon-btn" data-bs-toggle="tooltip" title="Créer une tâche">
                                            <i class="bi bi-plus-lg"></i>
                                        </a>
                                        <a href="{{ route('admin.tasks.index', ['project' => $project->id]) }}"
                                           class="tk-icon-btn" data-bs-toggle="tooltip" title="Voir les tâches">
                                            <i class="bi bi-list-task"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="tk-icon-btn" data-bs-toggle="dropdown"
                                                    aria-expanded="false" aria-label="Actions">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.projects.show', $project) }}">
                                                        <i class="bi bi-eye me-2"></i>Consulter
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.projects.edit', $project) }}">
                                                        <i class="bi bi-pencil me-2"></i>Modifier
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                                          data-tk-confirm="Supprimer le projet {{ $project->name }} ? Les tâches liées seront détachées.">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tk-pagination-bar">
                <form method="GET" action="{{ route('admin.projects.index') }}"
                      class="d-flex align-items-center gap-2">
                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                    <input type="hidden" name="active" value="{{ $filters['active'] }}">
                    <span>Affichage de {{ $projects->firstItem() }}–{{ $projects->lastItem() }}
                        sur {{ $projects->total() }}</span>
                    <select name="per_page" class="form-select form-select-sm" style="width:auto"
                            onchange="this.form.submit()" aria-label="Éléments par page">
                        @foreach ([10, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($filters['per_page'] === $size)>{{ $size }} / page</option>
                        @endforeach
                    </select>
                </form>

                {{ $projects->onEachSide(1)->links() }}
            </div>
        @endif
    </x-panel>
@endsection
