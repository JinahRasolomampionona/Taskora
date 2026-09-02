{{-- Fragment re-rendu seul lors d'un filtrage AJAX. --}}
@if ($tasks->isEmpty())
    <div class="tk-card">
        <x-empty-state icon="bi-inbox" title="Aucune demande trouvée"
                       text="Créez votre première demande : nos équipes la prendront en charge rapidement.">
            <a href="{{ route('client.tasks.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Nouvelle demande
            </a>
        </x-empty-state>
    </div>
@else
    <div class="row g-3">
        @foreach ($tasks as $task)
            <div class="col-md-6 col-xl-4">
                <x-task-card :task="$task" :url="route('client.tasks.show', $task)" :show-client="false" />
            </div>
        @endforeach
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
        <span class="tk-fs-sm tk-text-muted">
            {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} sur {{ $tasks->total() }} demande(s)
        </span>
        {{ $tasks->onEachSide(1)->links() }}
    </div>
@endif
