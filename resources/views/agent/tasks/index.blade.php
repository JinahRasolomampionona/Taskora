@extends('layouts.app')

@section('title', 'Mes tâches')

@section('content')
    <x-page-header title="Mes tâches"
                   subtitle="Toutes les tâches qui vous sont assignées."
                   :breadcrumbs="['Taskora' => route('agent.dashboard'), 'Mes tâches' => null]">
        <a href="{{ route('agent.tasks.board') }}" class="btn btn-light btn-sm">
            <i class="bi bi-kanban me-1"></i>Vue Kanban
        </a>
    </x-page-header>

    <div class="tk-filter-tabs mb-3">
        <a href="{{ route('agent.tasks.index') }}"
           class="tk-filter-tab {{ $filters['status'] === '' ? 'is-active' : '' }}">
            Toutes <span class="tk-filter-tab__count">{{ $counts['total'] }}</span>
        </a>
        @foreach (\App\Enums\TaskStatus::kanbanOrder() as $status)
            <a href="{{ route('agent.tasks.index', ['status' => $status->value]) }}"
               class="tk-filter-tab {{ $filters['status'] === $status->value ? 'is-active' : '' }}">
                <span class="tk-dot tk-dot-{{ str_replace('_', '-', $status->value) }}"></span>
                {{ $status->label() }}
                <span class="tk-filter-tab__count">{{ $counts[$status->value] }}</span>
            </a>
        @endforeach
    </div>

    <form class="tk-filters" method="GET" action="{{ route('agent.tasks.index') }}" data-tk-filters>
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label for="filter-q" class="form-label">Recherche</label>
                <input type="search" id="filter-q" name="q" value="{{ $filters['q'] }}"
                       class="form-control form-control-sm" placeholder="Code, titre, description…">
            </div>
            <div class="col-6 col-md-3">
                <label for="filter-status" class="form-label">Statut</label>
                <select id="filter-status" name="status" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach (\App\Enums\TaskStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="filter-priority" class="form-label">Priorité</label>
                <select id="filter-priority" name="priority" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    @foreach (\App\Enums\TaskPriority::options() as $value => $label)
                        <option value="{{ $value }}" @selected($filters['priority'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label for="filter-sort" class="form-label">Tri</label>
                <select id="filter-sort" name="sort" class="form-select form-select-sm">
                    <option value="latest" @selected($filters['sort'] === 'latest')>Récentes</option>
                    <option value="priority" @selected($filters['sort'] === 'priority')>Priorité</option>
                    <option value="due" @selected($filters['sort'] === 'due')>Échéance</option>
                </select>
            </div>
        </div>
    </form>

    <div data-tk-results>
        @include('agent.tasks.partials.grid')
    </div>
@endsection
