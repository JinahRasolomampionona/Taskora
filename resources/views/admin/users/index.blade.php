@extends('layouts.app')

@php
    $heading = match ($roleFilter?->value) {
        'user' => ['Agents', 'Les collaborateurs qui traitent les tâches.'],
        'client' => ['Clients', 'Les comptes qui déposent les demandes.'],
        'admin' => ['Administrateurs', 'Les comptes disposant des droits complets.'],
        default => ['Utilisateurs', 'Tous les comptes de la plateforme.'],
    };

    $createLabel = match ($roleFilter?->value) {
        'user' => 'Nouvel agent',
        'client' => 'Nouveau client',
        'admin' => 'Nouvel administrateur',
        default => 'Nouveau compte',
    };

    $currentRoute = $roleFilter?->value === 'user'
        ? route('admin.agents.index')
        : ($roleFilter?->value === 'client' ? route('admin.clients.index') : route('admin.users.index'));
@endphp

@section('title', $heading[0])

@section('content')
    <x-page-header :title="$heading[0]" :subtitle="$heading[1]"
                   :breadcrumbs="['Taskora' => route('admin.dashboard'), $heading[0] => null]">
        <a href="{{ route('admin.users.create', $roleFilter ? ['role' => $roleFilter->value] : []) }}"
           class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i>{{ $createLabel }}
        </a>
    </x-page-header>

    <div class="tk-filter-tabs mb-3">
        <a href="{{ route('admin.users.index') }}"
           class="tk-filter-tab {{ $roleFilter === null ? 'is-active' : '' }}">Tous</a>
        <a href="{{ route('admin.agents.index') }}"
           class="tk-filter-tab {{ $roleFilter?->value === 'user' ? 'is-active' : '' }}">Agents</a>
        <a href="{{ route('admin.clients.index') }}"
           class="tk-filter-tab {{ $roleFilter?->value === 'client' ? 'is-active' : '' }}">Clients</a>
        <a href="{{ route('admin.users.index', ['role' => 'admin']) }}"
           class="tk-filter-tab {{ $roleFilter?->value === 'admin' ? 'is-active' : '' }}">Administrateurs</a>
    </div>

    <x-panel flush>
        <div class="p-3 border-bottom">
            <form method="GET" action="{{ $currentRoute }}" class="d-flex flex-wrap align-items-center gap-2">
                @if ($roleFilter && ! request()->routeIs('admin.agents.index', 'admin.clients.index'))
                    <input type="hidden" name="role" value="{{ $roleFilter->value }}">
                @endif

                <div class="position-relative flex-grow-1" style="max-width:340px">
                    <i class="bi bi-search position-absolute tk-text-faint"
                       style="left:.7rem; top:50%; transform:translateY(-50%); font-size:.85rem;"></i>
                    <input type="search" name="q" value="{{ $filters['q'] }}"
                           class="form-control form-control-sm ps-4"
                           placeholder="Nom, e-mail, société…" aria-label="Rechercher un utilisateur">
                </div>

                <select name="active" class="form-select form-select-sm" style="width:auto" aria-label="État">
                    <option value="">Tous les états</option>
                    <option value="1" @selected($filters['active'] === '1')>Actifs</option>
                    <option value="0" @selected($filters['active'] === '0')>Désactivés</option>
                </select>

                <button type="submit" class="btn btn-light btn-sm"><i class="bi bi-funnel me-1"></i>Filtrer</button>
            </form>
        </div>

        @if ($users->isEmpty())
            <x-empty-state icon="bi-people" title="Aucun compte trouvé"
                           text="Ajustez la recherche ou créez un nouveau compte.">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Créer un compte</a>
            </x-empty-state>
        @else
            <div class="tk-table-wrap">
                <table class="table tk-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th style="width:150px">Rôle</th>
                            <th>Contact</th>
                            <th class="text-center" style="width:110px">Tâches</th>
                            <th style="width:110px">État</th>
                            <th class="text-end" style="width:120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" class="tk-user-chip">
                                        <x-avatar :user="$user" />
                                        <span class="tk-user-chip__meta">
                                            <span class="tk-user-chip__name">{{ $user->name }}</span>
                                            <span class="tk-user-chip__role">
                                                {{ $user->job_title ?? $user->company ?? '—' }}
                                            </span>
                                        </span>
                                    </a>
                                </td>
                                <td><x-role-badge :role="$user->role" /></td>
                                <td>
                                    <div class="tk-fs-xs">{{ $user->email }}</div>
                                    @if ($user->phone)
                                        <div class="tk-fs-xs tk-text-muted">{{ $user->phone }}</div>
                                    @endif
                                </td>
                                <td class="text-center tk-tabular">
                                    @if ($user->isClient())
                                        {{ $user->requests_count }}
                                    @else
                                        {{ $user->active_tasks_count }}
                                    @endif
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="tk-badge tk-badge-status-completed">Actif</span>
                                    @else
                                        <span class="tk-badge tk-badge-status-cancelled">Désactivé</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="tk-icon-btn" data-bs-toggle="tooltip" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @can('delete', $user)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  data-tk-confirm="Supprimer définitivement le compte de {{ $user->name }} ?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="tk-icon-btn text-danger"
                                                        data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tk-pagination-bar">
                <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} sur {{ $users->total() }} compte(s)</span>
                {{ $users->onEachSide(1)->links() }}
            </div>
        @endif
    </x-panel>
@endsection
