{{-- Fragment re-rendu seul lors d'un filtrage AJAX. --}}
@if ($tasks->isEmpty())
    <div class="tk-card">
        <x-empty-state icon="bi-search" title="Aucune tâche ne correspond à ces critères"
                       text="Ajustez la recherche ou les filtres, ou créez une nouvelle tâche.">
            <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Nouvelle tâche
            </a>
        </x-empty-state>
    </div>
@else
    <div class="row g-3">
        @foreach ($tasks as $task)
            <div class="col-md-6 col-xl-4 col-xxl-3">
                <x-task-card :task="$task" :url="route('admin.tasks.show', $task)" />
            </div>
        @endforeach
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
        <span class="tk-fs-sm tk-text-muted">
            {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} sur {{ $tasks->total() }} tâche(s)
        </span>
        {{ $tasks->onEachSide(1)->links() }}
    </div>
@endif
