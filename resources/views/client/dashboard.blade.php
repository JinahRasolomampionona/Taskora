@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <x-page-header :title="'Bonjour '.Str::before(auth()->user()->name, ' ')"
                   subtitle="Suivez l'avancement de vos demandes en un coup d'œil.">
        <a href="{{ route('client.tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle demande
        </a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <x-stat-card label="Demandes au total" :value="$overview['tasks']['total']" icon="bi-collection"
                         tone="primary" :href="route('client.tasks.index')" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="En attente" :value="$overview['tasks']['pending']" icon="bi-hourglass-split"
                         tone="warning" :href="route('client.tasks.index', ['status' => 'pending'])" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="En cours" :value="$overview['tasks']['in_progress']" icon="bi-play-circle"
                         tone="info" :href="route('client.tasks.index', ['status' => 'in_progress'])" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Terminées" :value="$overview['tasks']['completed']" icon="bi-check-circle"
                         tone="success" :href="route('client.tasks.index', ['status' => 'completed'])" />
        </div>
    </div>

    <x-panel title="Mes demandes en cours">
        <x-slot:actions>
            <a href="{{ route('client.tasks.index') }}" class="btn btn-light btn-sm">Toutes mes demandes</a>
        </x-slot:actions>

        @if ($tasks->isEmpty())
            <x-empty-state icon="bi-inbox" title="Aucune demande en cours"
                           text="Déposez une demande : elle sera prise en charge par nos équipes.">
                <a href="{{ route('client.tasks.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Nouvelle demande
                </a>
            </x-empty-state>
        @else
            <div class="row g-3">
                @foreach ($tasks as $task)
                    <div class="col-md-6 col-xl-4">
                        <x-task-card :task="$task" :url="route('client.tasks.show', $task)"
                                     :show-client="false" />
                    </div>
                @endforeach
            </div>
        @endif
    </x-panel>

    @if ($recent->isNotEmpty())
        <x-panel title="Demandes récemment terminées" flush class="mt-3">
            <div class="tk-table-wrap">
                <table class="table tk-table">
                    <thead>
                        <tr>
                            <th style="width:100px">Code</th>
                            <th>Demande</th>
                            <th style="width:180px">Traitée par</th>
                            <th class="text-end" style="width:140px">Terminée le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recent as $task)
                            <tr>
                                <td><span class="tk-table__code">{{ $task->code }}</span></td>
                                <td>
                                    <a href="{{ route('client.tasks.show', $task) }}"
                                       class="tk-table__link">{{ $task->title }}</a>
                                </td>
                                <td><x-user-chip :user="$task->agent" size="sm" :sub="false" placeholder="—" /></td>
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
