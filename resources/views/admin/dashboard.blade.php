@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <x-page-header title="Tableau de bord"
                   subtitle="Vue d'ensemble de l'activité Taskora — {{ now()->translatedFormat('l j F Y') }}">
        <a href="{{ route('admin.tasks.board') }}" class="btn btn-light btn-sm">
            <i class="bi bi-kanban me-1"></i>Tableau Kanban
        </a>

        <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                    id="tk-create-menu" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-plus-lg me-1"></i>Créer
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="tk-create-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.tasks.create') }}">
                        <i class="bi bi-check2-square me-2"></i>Nouvelle tâche
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.projects.create') }}">
                        <i class="bi bi-folder-plus me-2"></i>Nouveau projet
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.users.create', ['role' => 'user']) }}">
                        <i class="bi bi-person-badge me-2"></i>Nouvel agent
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.users.create', ['role' => 'client']) }}">
                        <i class="bi bi-people me-2"></i>Nouveau client
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.users.create', ['role' => 'admin']) }}">
                        <i class="bi bi-shield-lock me-2"></i>Nouvel administrateur
                    </a>
                </li>
            </ul>
        </div>
    </x-page-header>

    {{-- Statistiques principales --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <x-stat-card label="Tâches au total" :value="$overview['tasks']['total']" icon="bi-collection"
                         tone="primary" :href="route('admin.tasks.index')"
                         :hint="$overview['tasks']['active'].' en cours de traitement'" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="En attente" :value="$overview['tasks']['pending']" icon="bi-hourglass-split"
                         tone="warning" :href="route('admin.tasks.index', ['status' => 'pending'])"
                         :hint="$overview['unassigned'].' sans agent assigné'" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="En cours" :value="$overview['tasks']['in_progress']" icon="bi-play-circle"
                         tone="info" :href="route('admin.tasks.index', ['status' => 'in_progress'])"
                         :hint="$overview['tasks']['todo'].' tâches à démarrer'" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Terminées" :value="$overview['tasks']['completed']" icon="bi-check-circle"
                         tone="success" :href="route('admin.tasks.index', ['status' => 'completed'])"
                         :hint="$overview['tasks']['archived'].' archivées'" />
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <x-stat-card label="Urgentes actives" :value="$overview['urgent']" icon="bi-exclamation-triangle"
                         tone="danger" :href="route('admin.tasks.index', ['priority' => 'urgent'])" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="En retard" :value="$overview['overdue']" icon="bi-clock-history"
                         tone="danger" :href="route('admin.tasks.index', ['overdue' => 1])" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Agents" :value="$overview['agents']" icon="bi-person-badge"
                         :href="route('admin.agents.index')" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Clients" :value="$overview['clients']" icon="bi-people"
                         :href="route('admin.clients.index')" />
        </div>
    </div>

    {{-- Raccourcis de création --}}
    <x-panel title="Actions rapides" class="mb-3">
        <div class="row g-2">
            @foreach ([
                ['Nouvelle tâche', 'Créer et assigner une tâche', 'bi-check2-square', route('admin.tasks.create')],
                ['Nouveau projet', 'Regrouper les tâches d\'une équipe', 'bi-folder-plus', route('admin.projects.create')],
                ['Nouvel agent', 'Ouvrir un accès de traitement', 'bi-person-badge', route('admin.users.create', ['role' => 'user'])],
                ['Nouveau client', 'Ouvrir un espace de demandes', 'bi-people', route('admin.users.create', ['role' => 'client'])],
            ] as [$label, $hint, $icon, $url])
                <div class="col-sm-6 col-xl-3">
                    <a href="{{ $url }}" class="tk-quick-action">
                        <span class="tk-quick-action__icon"><i class="bi {{ $icon }}"></i></span>
                        <span class="min-w-0">
                            <span class="tk-quick-action__label">{{ $label }}</span>
                            <span class="tk-quick-action__hint">{{ $hint }}</span>
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
    </x-panel>

    {{-- Projets --}}
    <x-panel title="Projets actifs" class="mb-3">
        <x-slot:actions>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-light btn-sm">Tous les projets</a>
        </x-slot:actions>

        @if ($projects->isEmpty())
            <x-empty-state icon="bi-folder2-open" title="Aucun projet actif" small
                           text="Regroupez vos tâches par projet pour suivre l'activité de chaque équipe.">
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">Créer un projet</a>
            </x-empty-state>
        @else
            <div class="row g-3">
                @foreach ($projects as $project)
                    <div class="col-sm-6 col-xl-3">
                        <div class="tk-card h-100 tk-reveal" style="box-shadow:none">
                            <div class="tk-card__body">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <x-avatar :name="$project->initials" :color="$project->color" size="lg" />
                                    <div class="min-w-0">
                                        <div class="fw-semibold tk-fs-sm tk-truncate">{{ $project->name }}</div>
                                        <div class="tk-fs-xs tk-text-faint">{{ $project->code }}</div>
                                    </div>
                                </div>

                                <p class="tk-fs-sm tk-text-muted mb-3">
                                    <span class="fw-semibold text-dark tk-tabular">{{ $project->tasks_count }}</span> tâches
                                    et
                                    <span class="fw-semibold text-dark tk-tabular">{{ $project->members_count }}</span> contributeurs
                                </p>

                                <div class="d-flex align-items-center gap-3 tk-fs-sm">
                                    <a href="{{ route('admin.projects.show', $project) }}">Voir détails</a>
                                    <a href="{{ route('admin.tasks.index', ['project' => $project->id]) }}"
                                       class="tk-text-muted">Tâches</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-panel>

    <div class="row g-3">
        {{-- Dernières activités --}}
        <div class="col-xl-7">
            <x-panel title="Dernières activités des tâches" flush class="h-100">
                @if ($recentActivity->isEmpty())
                    <x-empty-state icon="bi-activity" title="Aucune activité enregistrée" small />
                @else
                    <div class="tk-table-wrap">
                        <table class="table tk-table">
                            <thead>
                                <tr>
                                    <th>Tâche</th>
                                    <th>Par</th>
                                    <th>Transition</th>
                                    <th class="text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentActivity as $entry)
                                    <tr>
                                        <td style="max-width:230px">
                                            @if ($entry->task)
                                                <a href="{{ route('admin.tasks.show', $entry->task_id) }}"
                                                   class="tk-table__link d-block tk-truncate">{{ $entry->task->title }}</a>
                                                <span class="tk-table__code">{{ $entry->task->code }}</span>
                                            @else
                                                <span class="tk-text-faint">Tâche supprimée</span>
                                            @endif
                                        </td>
                                        <td class="tk-fs-xs">{{ $entry->user?->name ?? 'Système' }}</td>
                                        <td>
                                            <span class="d-inline-flex align-items-center gap-1 flex-wrap">
                                                @if ($entry->old_status)
                                                    <x-status-badge :status="$entry->old_status" />
                                                    <i class="bi bi-arrow-right tk-text-faint tk-fs-xs"></i>
                                                @endif
                                                <x-status-badge :status="$entry->new_status" />
                                            </span>
                                        </td>
                                        <td class="text-end tk-fs-xs tk-text-muted text-nowrap">
                                            {{ $entry->created_at->diffForHumans(short: true) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel>
        </div>

        {{-- Charge des agents + commentaires --}}
        <div class="col-xl-5">
            <x-panel title="Charge des agents" class="mb-3">
                @if ($workload->isEmpty())
                    <x-empty-state icon="bi-person-badge" title="Aucun agent actif" small />
                @else
                    <div class="d-grid gap-3">
                        @foreach ($workload as $agent)
                            @php
                                $total = max(1, $workload->max('active_tasks_count'));
                                $ratio = (int) round($agent->active_tasks_count / $total * 100);
                            @endphp
                            <div>
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <x-user-chip :user="$agent" :sub="$agent->job_title" size="sm" />
                                    <span class="tk-fs-xs tk-text-muted text-nowrap tk-tabular">
                                        {{ $agent->active_tasks_count }} active(s)
                                        @if ($agent->urgent_tasks_count > 0)
                                            · <span class="text-danger fw-semibold">{{ $agent->urgent_tasks_count }} urgente(s)</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="progress" role="progressbar" aria-valuenow="{{ $ratio }}"
                                     aria-valuemin="0" aria-valuemax="100"
                                     aria-label="Charge de {{ $agent->name }}">
                                    <div class="progress-bar" style="width: {{ $ratio }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-panel>

            <x-panel title="Derniers commentaires des tâches">
                @if ($recentComments->isEmpty())
                    <x-empty-state icon="bi-chat-left-text" title="Aucun commentaire" small />
                @else
                    <div class="d-grid">
                        @foreach ($recentComments as $comment)
                            <div class="tk-comment">
                                <x-avatar :user="$comment->user" size="sm" />
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-semibold tk-fs-sm">{{ $comment->user->name }}</span>
                                        <span class="tk-fs-xs tk-text-faint">{{ $comment->created_at->diffForHumans(short: true) }}</span>
                                        @if ($comment->is_internal)
                                            <span class="tk-badge tk-badge-neutral">Interne</span>
                                        @endif
                                    </div>
                                    @if ($comment->task)
                                        <a href="{{ route('admin.tasks.show', $comment->task_id) }}"
                                           class="tk-fs-xs tk-text-muted d-block tk-truncate">{{ $comment->task->code }} · {{ $comment->task->title }}</a>
                                    @endif
                                    <div class="tk-comment__body">{{ Str::limit($comment->body, 120) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-panel>
        </div>
    </div>

    {{-- Tâches urgentes --}}
    @if ($urgentTasks->isNotEmpty())
        <x-panel title="Tâches urgentes à traiter" class="mt-3">
            <x-slot:actions>
                <a href="{{ route('admin.tasks.index', ['priority' => 'urgent']) }}"
                   class="btn btn-light btn-sm">Tout voir</a>
            </x-slot:actions>

            <div class="row g-3">
                @foreach ($urgentTasks as $task)
                    <div class="col-md-6 col-xl-4">
                        <x-task-card :task="$task" :url="route('admin.tasks.show', $task)" />
                    </div>
                @endforeach
            </div>
        </x-panel>
    @endif
@endsection
