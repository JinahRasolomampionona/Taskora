@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <x-page-header :title="$user->name"
                   :subtitle="$user->job_title ?? $user->company"
                   :breadcrumbs="[
                       'Taskora' => route('admin.dashboard'),
                       'Utilisateurs' => route('admin.users.index'),
                       $user->name => null,
                   ]">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @can('delete', $user)
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  data-tk-confirm="Supprimer définitivement le compte de {{ $user->name }} ?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-light btn-sm text-danger">
                    <i class="bi bi-trash me-1"></i>Supprimer
                </button>
            </form>
        @endcan
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-4">
            <x-panel title="Profil">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <x-avatar :user="$user" size="xl" />
                    <div class="min-w-0">
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <x-role-badge :role="$user->role" />
                    </div>
                </div>

                <div class="tk-meta-list">
                    <div>
                        <div class="tk-meta-item__label">Adresse e-mail</div>
                        <div class="tk-meta-item__value text-break">{{ $user->email }}</div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">Téléphone</div>
                        <div class="tk-meta-item__value">{{ $user->phone ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">Société</div>
                        <div class="tk-meta-item__value">{{ $user->company ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">État du compte</div>
                        <div>
                            @if ($user->is_active)
                                <span class="tk-badge tk-badge-status-completed">Actif</span>
                            @else
                                <span class="tk-badge tk-badge-status-cancelled">Désactivé</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">Dernière connexion</div>
                        <div class="tk-meta-item__value">
                            {{ $user->last_login_at?->format('d/m/Y à H:i') ?? 'Jamais connecté' }}
                        </div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">Compte créé le</div>
                        <div class="tk-meta-item__value">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </x-panel>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-3">
                @if ($user->isClient())
                    <div class="col-sm-6">
                        <x-stat-card label="Demandes déposées" :value="$user->requests_count"
                                     icon="bi-inbox" tone="primary"
                                     :href="route('admin.tasks.index', ['client' => $user->id])" />
                    </div>
                @else
                    <div class="col-sm-6">
                        <x-stat-card label="Tâches actives" :value="$user->active_tasks_count"
                                     icon="bi-play-circle" tone="info"
                                     :href="route('admin.tasks.index', ['agent' => $user->id])" />
                    </div>
                    <div class="col-sm-6">
                        <x-stat-card label="Tâches terminées" :value="$user->completed_tasks_count"
                                     icon="bi-check-circle" tone="success" />
                    </div>
                @endif
            </div>

            <x-panel :title="$user->isClient() ? 'Dernières demandes' : 'Dernières tâches assignées'" flush>
                @if ($recentTasks->isEmpty())
                    <x-empty-state icon="bi-inbox" title="Aucune tâche"
                                   text="Ce compte n'est encore lié à aucune tâche." />
                @else
                    <div class="tk-table-wrap">
                        <table class="table tk-table">
                            <thead>
                                <tr>
                                    <th style="width:100px">Code</th>
                                    <th>Titre</th>
                                    <th style="width:130px">Statut</th>
                                    <th style="width:110px">Priorité</th>
                                    <th class="text-end" style="width:110px">Créée le</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentTasks as $task)
                                    <tr>
                                        <td><span class="tk-table__code">{{ $task->code }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.tasks.show', $task) }}"
                                               class="tk-table__link">{{ $task->title }}</a>
                                        </td>
                                        <td><x-status-badge :status="$task->status" /></td>
                                        <td><x-priority-badge :priority="$task->priority" /></td>
                                        <td class="text-end tk-fs-xs tk-text-muted">
                                            {{ $task->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel>
        </div>
    </div>
@endsection
