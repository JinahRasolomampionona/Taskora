@extends('layouts.app')

@section('title', 'Tableau Kanban')

@section('content')
    <x-page-header title="Tableau Kanban"
                   subtitle="Glissez une carte d'une colonne à l'autre pour faire évoluer son statut."
                   :breadcrumbs="['Taskora' => route('admin.dashboard'), 'Tableau' => null]">
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-grid-3x3-gap me-1"></i>Vue liste
        </a>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle tâche
        </a>
    </x-page-header>

    <form class="tk-filters" method="GET" action="{{ route('admin.tasks.board') }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label for="board-q" class="form-label">Recherche</label>
                <input type="search" id="board-q" name="q" value="{{ $filters['q'] }}"
                       class="form-control form-control-sm" placeholder="Code, titre, description…">
            </div>
            <div class="col-6 col-md-3">
                <label for="board-agent" class="form-label">Agent</label>
                <select id="board-agent" name="agent" class="form-select form-select-sm">
                    <option value="">Tous les agents</option>
                    <option value="none" @selected($filters['agent'] === 'none')>Non assignées</option>
                    @foreach ($agents as $agent)
                        <option value="{{ $agent->id }}" @selected((string) $filters['agent'] === (string) $agent->id)>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="board-priority" class="form-label">Priorité</label>
                <select id="board-priority" name="priority" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    @foreach (\App\Enums\TaskPriority::options() as $value => $label)
                        <option value="{{ $value }}" @selected($filters['priority'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filtrer</button>
                <a href="{{ route('admin.tasks.board') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </div>
    </form>

    <div class="tk-board" data-tk-board="draggable">
        @foreach ($columns as $key => $column)
            @php
                $status = $column['status'];
                $dotClass = 'tk-dot-'.str_replace('_', '-', $status->value);
            @endphp

            <section class="tk-board__column" data-status="{{ $status->value }}" data-tk-board-column>
                <header class="tk-board__head">
                    <span class="tk-dot {{ $dotClass }}"></span>
                    <span class="tk-board__title">{{ $status->label() }}</span>
                    <span class="tk-board__count" data-tk-board-count>{{ $column['tasks']->count() }}</span>
                </header>

                <div class="tk-board__list" data-tk-board-list="{{ $status->value }}">
                    @foreach ($column['tasks'] as $task)
                        <x-task-card :task="$task"
                                     :url="route('admin.tasks.show', $task)"
                                     :draggable="true"
                                     :show-status="false" />
                    @endforeach

                    <div class="tk-board__empty {{ $column['tasks']->isNotEmpty() ? 'd-none' : '' }}">
                        Aucune tâche
                    </div>
                </div>
            </section>
        @endforeach
    </div>
@endsection
