@extends('layouts.app')

@section('title', 'Tâches')

@section('content')
    <x-page-header title="Tâches"
                   subtitle="Recherchez, filtrez et distribuez le travail de vos équipes."
                   :breadcrumbs="['Taskora' => route('admin.dashboard'), 'Tâches' => null]">
        <a href="{{ route('admin.tasks.board') }}" class="btn btn-light btn-sm">
            <i class="bi bi-kanban me-1"></i>Vue Kanban
        </a>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle tâche
        </a>
    </x-page-header>

    {{-- Raccourcis par statut --}}
    <div class="tk-filter-tabs mb-3">
        <a href="{{ route('admin.tasks.index') }}"
           class="tk-filter-tab {{ $filters['status'] === '' ? 'is-active' : '' }}">
            Toutes <span class="tk-filter-tab__count">{{ $counts['total'] - $counts['archived'] }}</span>
        </a>
        @foreach (\App\Enums\TaskStatus::kanbanOrder() as $status)
            <a href="{{ route('admin.tasks.index', ['status' => $status->value]) }}"
               class="tk-filter-tab {{ $filters['status'] === $status->value ? 'is-active' : '' }}">
                <span class="tk-dot tk-dot-{{ str_replace('_', '-', $status->value) }}"></span>
                {{ $status->label() }}
                <span class="tk-filter-tab__count">{{ $counts[$status->value] }}</span>
            </a>
        @endforeach
    </div>

    @include('admin.tasks.partials.filters')

    <div data-tk-results>
        @include('admin.tasks.partials.grid')
    </div>
@endsection
