@extends('layouts.app')

@section('title', 'Tableau Kanban')

@section('content')
    <x-page-header title="Mon tableau"
                   subtitle="Faites glisser une carte pour faire avancer votre travail."
                   :breadcrumbs="['Taskora' => route('agent.dashboard'), 'Tableau' => null]">
        <a href="{{ route('agent.tasks.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-grid-3x3-gap me-1"></i>Vue liste
        </a>
    </x-page-header>

    <form class="tk-filters" method="GET" action="{{ route('agent.tasks.board') }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label for="board-q" class="form-label">Recherche</label>
                <input type="search" id="board-q" name="q" value="{{ $filters['q'] }}"
                       class="form-control form-control-sm" placeholder="Code, titre, description…">
            </div>
            <div class="col-8 col-md-4">
                <label for="board-priority" class="form-label">Priorité</label>
                <select id="board-priority" name="priority" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    @foreach (\App\Enums\TaskPriority::options() as $value => $label)
                        <option value="{{ $value }}" @selected($filters['priority'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filtrer</button>
            </div>
        </div>
    </form>

    <div class="tk-board" data-tk-board="draggable">
        @foreach ($columns as $column)
            @php $status = $column['status']; @endphp

            <section class="tk-board__column" data-status="{{ $status->value }}" data-tk-board-column>
                <header class="tk-board__head">
                    <span class="tk-dot tk-dot-{{ str_replace('_', '-', $status->value) }}"></span>
                    <span class="tk-board__title">{{ $status->label() }}</span>
                    <span class="tk-board__count" data-tk-board-count>{{ $column['tasks']->count() }}</span>
                </header>

                <div class="tk-board__list" data-tk-board-list="{{ $status->value }}">
                    @foreach ($column['tasks'] as $task)
                        <x-task-card :task="$task"
                                     :url="route('agent.tasks.show', $task)"
                                     :draggable="true"
                                     :show-status="false"
                                     :show-agent="false" />
                    @endforeach

                    <div class="tk-board__empty {{ $column['tasks']->isNotEmpty() ? 'd-none' : '' }}">
                        Aucune tâche
                    </div>
                </div>
            </section>
        @endforeach
    </div>
@endsection
