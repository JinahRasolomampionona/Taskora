{{-- Fragment re-rendu seul lors d'un filtrage AJAX. --}}
@if ($tasks->isEmpty())
    <div class="tk-card">
        <x-empty-state icon="bi-inbox" title="Aucune tâche ne correspond"
                       text="Modifiez vos filtres ou revenez plus tard : les nouvelles assignations apparaîtront ici." />
    </div>
@else
    <div class="row g-3">
        @foreach ($tasks as $task)
            <div class="col-md-6 col-xl-4 col-xxl-3">
                <x-task-card :task="$task" :url="route('agent.tasks.show', $task)" :show-agent="false" />
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
