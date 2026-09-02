@extends('layouts.app')

@section('title', 'Archives')

@section('content')
    <x-page-header title="Archives"
                   subtitle="Consultez les tâches sorties du flux de travail actif."
                   :breadcrumbs="['Taskora' => route('admin.dashboard'), 'Archives' => null]" />

    <div class="tk-filter-tabs mb-3">
        <a href="{{ route('admin.archives.index') }}"
           class="tk-filter-tab {{ $scope === \App\Enums\TaskStatus::Archived ? 'is-active' : '' }}">
            <span class="tk-dot tk-dot-archived"></span>
            Archivées <span class="tk-filter-tab__count">{{ $archivedCount }}</span>
        </a>
        <a href="{{ route('admin.archives.index', ['scope' => 'cancelled']) }}"
           class="tk-filter-tab {{ $scope === \App\Enums\TaskStatus::Cancelled ? 'is-active' : '' }}">
            <span class="tk-dot tk-dot-cancelled"></span>
            Annulées <span class="tk-filter-tab__count">{{ $cancelledCount }}</span>
        </a>
    </div>

    <form class="tk-filters" method="GET" action="{{ route('admin.archives.index') }}">
        <input type="hidden" name="scope" value="{{ $scope === \App\Enums\TaskStatus::Cancelled ? 'cancelled' : 'archived' }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label for="archive-q" class="form-label">Recherche</label>
                <input type="search" id="archive-q" name="q" value="{{ $filters['q'] }}"
                       class="form-control form-control-sm" placeholder="Code, titre, description…">
            </div>
            <div class="col-8 col-md-4">
                <label for="archive-sort" class="form-label">Tri</label>
                <select id="archive-sort" name="sort" class="form-select form-select-sm">
                    <option value="latest" @selected($filters['sort'] === 'latest')>Plus récentes</option>
                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Plus anciennes</option>
                    <option value="title" @selected($filters['sort'] === 'title')>Titre</option>
                </select>
            </div>
            <div class="col-4 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filtrer</button>
            </div>
        </div>
    </form>

    <x-panel flush>
        @if ($tasks->isEmpty())
            <x-empty-state icon="bi-archive"
                           :title="$scope === \App\Enums\TaskStatus::Cancelled ? 'Aucune tâche annulée' : 'Aucune tâche archivée'"
                           text="Les tâches terminées peuvent être archivées depuis leur page de détail." />
        @else
            <div class="tk-table-wrap">
                <table class="table tk-table">
                    <thead>
                        <tr>
                            <th style="width:100px">Code</th>
                            <th>Titre</th>
                            <th style="width:170px">Client</th>
                            <th style="width:170px">Agent</th>
                            <th style="width:110px">Priorité</th>
                            <th class="text-end" style="width:120px">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td><span class="tk-table__code">{{ $task->code }}</span></td>
                                <td>
                                    <a href="{{ route('admin.tasks.show', $task) }}"
                                       class="tk-table__link">{{ $task->title }}</a>
                                    @if ($task->project)
                                        <div class="tk-fs-xs tk-text-muted">{{ $task->project->name }}</div>
                                    @endif
                                </td>
                                <td><x-user-chip :user="$task->client" size="sm" :sub="false" placeholder="—" /></td>
                                <td><x-user-chip :user="$task->agent" size="sm" :sub="false" placeholder="—" /></td>
                                <td><x-priority-badge :priority="$task->priority" /></td>
                                <td class="text-end tk-fs-xs tk-text-muted">
                                    {{ ($task->archived_at ?? $task->cancelled_at ?? $task->updated_at)->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tk-pagination-bar">
                <span>{{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} sur {{ $tasks->total() }} tâche(s)</span>
                {{ $tasks->onEachSide(1)->links() }}
            </div>
        @endif
    </x-panel>
@endsection
