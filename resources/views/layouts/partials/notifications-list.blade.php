@php
    $user = auth()->user();

    // Alertes calculées à partir des compteurs déjà chargés : aucune requête ici.
    $items = collect();

    if ($user->isAdmin()) {
        if (($navCounts['unassigned'] ?? 0) > 0) {
            $items->push([
                'icon' => 'bi-person-dash',
                'tone' => 'warning',
                'text' => $navCounts['unassigned'].' tâche(s) sans agent assigné',
                'url' => route('admin.tasks.index', ['agent' => 'none']),
            ]);
        }
        if (($navCounts['pending'] ?? 0) > 0) {
            $items->push([
                'icon' => 'bi-hourglass-split',
                'tone' => 'info',
                'text' => $navCounts['pending'].' demande(s) en attente de traitement',
                'url' => route('admin.tasks.index', ['status' => 'pending']),
            ]);
        }
    } elseif ($user->isAgent()) {
        if (($navCounts['todo'] ?? 0) > 0) {
            $items->push([
                'icon' => 'bi-list-check',
                'tone' => 'info',
                'text' => $navCounts['todo'].' tâche(s) à démarrer',
                'url' => route('agent.tasks.index', ['status' => 'todo']),
            ]);
        }
        if (($navCounts['in_progress'] ?? 0) > 0) {
            $items->push([
                'icon' => 'bi-play-circle',
                'tone' => 'primary',
                'text' => $navCounts['in_progress'].' tâche(s) en cours',
                'url' => route('agent.tasks.index', ['status' => 'in_progress']),
            ]);
        }
    } else {
        if (($navCounts['pending'] ?? 0) > 0) {
            $items->push([
                'icon' => 'bi-hourglass-split',
                'tone' => 'warning',
                'text' => $navCounts['pending'].' demande(s) en attente de prise en charge',
                'url' => route('client.tasks.index', ['status' => 'pending']),
            ]);
        }
        if (($navCounts['completed'] ?? 0) > 0) {
            $items->push([
                'icon' => 'bi-check-circle',
                'tone' => 'success',
                'text' => $navCounts['completed'].' demande(s) terminée(s)',
                'url' => route('client.tasks.index', ['status' => 'completed']),
            ]);
        }
    }
@endphp

@forelse ($items as $item)
    <a href="{{ $item['url'] }}" class="dropdown-item d-flex align-items-start gap-2 py-2 text-wrap">
        <i class="bi {{ $item['icon'] }} text-{{ $item['tone'] }} mt-1"></i>
        <span class="tk-fs-sm">{{ $item['text'] }}</span>
    </a>
@empty
    <div class="tk-empty tk-empty--sm">
        <div class="tk-empty__icon"><i class="bi bi-bell-slash"></i></div>
        <div class="tk-fs-sm">Aucune alerte en cours</div>
    </div>
@endforelse
