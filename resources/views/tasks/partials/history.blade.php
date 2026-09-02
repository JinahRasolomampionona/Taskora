{{-- Historique des changements de statut ($task->statusHistories doit être chargé). --}}
<x-panel title="Historique">
    @if ($task->statusHistories->isEmpty())
        <x-empty-state icon="bi-clock-history" title="Aucun historique"
                       text="Les changements de statut seront tracés ici." small />
    @else
        <div class="tk-timeline">
            @foreach ($task->statusHistories as $entry)
                <div class="tk-timeline__item">
                    <div class="d-flex align-items-center gap-1 flex-wrap mb-1">
                        @if ($entry->old_status)
                            <x-status-badge :status="$entry->old_status" />
                            <i class="bi bi-arrow-right tk-text-faint tk-fs-xs"></i>
                        @endif
                        <x-status-badge :status="$entry->new_status" />
                    </div>

                    <div class="tk-timeline__meta">
                        {{ $entry->user?->name ?? 'Système' }}
                        · {{ $entry->created_at->format('d/m/Y à H:i') }}
                    </div>

                    @if ($entry->comment)
                        <div class="tk-fs-sm tk-text-muted mt-1">{{ $entry->comment }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-panel>
