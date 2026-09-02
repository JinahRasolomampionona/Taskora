@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <x-page-header :title="'Bonjour '.Str::before(auth()->user()->name, ' ')"
                   :subtitle="'Voici votre plan de travail du '.now()->translatedFormat('l j F Y').'.'">
        <a href="{{ route('agent.tasks.board') }}" class="btn btn-light btn-sm">
            <i class="bi bi-kanban me-1"></i>Vue Kanban
        </a>
        <a href="{{ route('agent.tasks.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-list-task me-1"></i>Toutes mes tâches
        </a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <x-stat-card label="À faire" :value="$overview['tasks']['todo']" icon="bi-list-check"
                         tone="primary" :href="route('agent.tasks.index', ['status' => 'todo'])" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="En cours" :value="$overview['tasks']['in_progress']" icon="bi-play-circle"
                         tone="info" :href="route('agent.tasks.index', ['status' => 'in_progress'])" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Urgentes" :value="$overview['urgent']" icon="bi-exclamation-triangle"
                         tone="danger" :href="route('agent.tasks.index', ['priority' => 'urgent'])" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Terminées" :value="$overview['tasks']['completed']" icon="bi-check-circle"
                         tone="success" :href="route('agent.tasks.history')" />
        </div>
    </div>

    @if ($overview['overdue'] > 0)
        <div class="alert alert-warning d-flex align-items-center gap-2 tk-fs-sm" role="alert">
            <i class="bi bi-clock-history"></i>
            <span>
                <strong>{{ $overview['overdue'] }}</strong> tâche(s) ont dépassé leur date limite.
            </span>
            <a href="{{ route('agent.tasks.index', ['overdue' => 1]) }}"
               class="ms-auto btn btn-sm btn-light">Les consulter</a>
        </div>
    @endif

    <x-panel title="Mes tâches prioritaires">
        <x-slot:actions>
            <a href="{{ route('agent.tasks.index') }}" class="btn btn-light btn-sm">Tout voir</a>
        </x-slot:actions>

        @if ($tasks->isEmpty())
            <x-empty-state icon="bi-emoji-smile" title="Rien à traiter pour le moment"
                           text="Aucune tâche active ne vous est assignée. Profitez-en !" />
        @else
            <div class="row g-3">
                @foreach ($tasks as $task)
                    <div class="col-md-6 col-xl-4">
                        <x-task-card :task="$task" :url="route('agent.tasks.show', $task)"
                                     :show-agent="false" />
                    </div>
                @endforeach
            </div>
        @endif
    </x-panel>

    @if ($recentlyCompleted->isNotEmpty())
        <x-panel title="Dernières tâches terminées" flush class="mt-3">
            <div class="tk-table-wrap">
                <table class="table tk-table">
                    <thead>
                        <tr>
                            <th style="width:100px">Code</th>
                            <th>Titre</th>
                            <th style="width:180px">Client</th>
                            <th class="text-end" style="width:140px">Terminée le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentlyCompleted as $task)
                            <tr>
                                <td><span class="tk-table__code">{{ $task->code }}</span></td>
                                <td>
                                    <a href="{{ route('agent.tasks.show', $task) }}"
                                       class="tk-table__link">{{ $task->title }}</a>
                                </td>
                                <td><x-user-chip :user="$task->client" size="sm" :sub="false" placeholder="—" /></td>
                                <td class="text-end tk-fs-xs tk-text-muted">
                                    {{ $task->completed_at?->format('d/m/Y') ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-panel>
    @endif
@endsection
