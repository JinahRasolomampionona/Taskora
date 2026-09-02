@extends('layouts.app')

@section('title', 'Historique')

@section('content')
    <x-page-header title="Historique"
                   subtitle="Vos tâches terminées, annulées et archivées."
                   :breadcrumbs="['Taskora' => route('agent.dashboard'), 'Historique' => null]" />

    <form class="tk-filters" method="GET" action="{{ route('agent.tasks.history') }}">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-9">
                <label for="history-q" class="form-label">Recherche</label>
                <input type="search" id="history-q" name="q" value="{{ $filters['q'] }}"
                       class="form-control form-control-sm" placeholder="Code, titre, description…">
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-1"></i>Rechercher
                </button>
            </div>
        </div>
    </form>

    <x-panel flush>
        @if ($tasks->isEmpty())
            <x-empty-state icon="bi-clock-history" title="Aucune tâche dans l'historique"
                           text="Les tâches que vous terminerez apparaîtront ici." />
        @else
            <div class="tk-table-wrap">
                <table class="table tk-table">
                    <thead>
                        <tr>
                            <th style="width:100px">Code</th>
                            <th>Titre</th>
                            <th style="width:170px">Client</th>
                            <th style="width:130px">Statut</th>
                            <th style="width:110px">Estimation</th>
                            <th class="text-end" style="width:120px">Mise à jour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td><span class="tk-table__code">{{ $task->code }}</span></td>
                                <td>
                                    <a href="{{ route('agent.tasks.show', $task) }}"
                                       class="tk-table__link">{{ $task->title }}</a>
                                    @if ($task->project)
                                        <div class="tk-fs-xs tk-text-muted">{{ $task->project->name }}</div>
                                    @endif
                                </td>
                                <td><x-user-chip :user="$task->client" size="sm" :sub="false" placeholder="—" /></td>
                                <td><x-status-badge :status="$task->status" /></td>
                                <td class="tk-fs-xs">{{ $task->estimated_for_humans ?? '—' }}</td>
                                <td class="text-end tk-fs-xs tk-text-muted">{{ $task->updated_at->format('d/m/Y') }}</td>
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
