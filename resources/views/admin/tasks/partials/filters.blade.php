{{--
    Barre de filtres administrateur.
    Soumise en AJAX par task-filters.js ; fonctionne aussi sans JavaScript.
--}}
<form class="tk-filters" method="GET" action="{{ route('admin.tasks.index') }}" data-tk-filters>
    <div class="row g-2 align-items-end">
        <div class="col-12 col-lg-3">
            <label for="filter-q" class="form-label">Recherche</label>
            <input type="search" id="filter-q" name="q" value="{{ $filters['q'] }}"
                   class="form-control form-control-sm"
                   placeholder="Code, titre, description…">
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <label for="filter-status" class="form-label">Statut</label>
            <select id="filter-status" name="status" class="form-select form-select-sm">
                <option value="">Tous les statuts</option>
                @foreach (\App\Enums\TaskStatus::options() as $value => $label)
                    <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <label for="filter-priority" class="form-label">Priorité</label>
            <select id="filter-priority" name="priority" class="form-select form-select-sm">
                <option value="">Toutes</option>
                @foreach (\App\Enums\TaskPriority::options() as $value => $label)
                    <option value="{{ $value }}" @selected($filters['priority'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <label for="filter-agent" class="form-label">Agent</label>
            <select id="filter-agent" name="agent" class="form-select form-select-sm">
                <option value="">Tous les agents</option>
                <option value="none" @selected($filters['agent'] === 'none')>Non assignées</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}" @selected((string) $filters['agent'] === (string) $agent->id)>
                        {{ $agent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-6 col-lg-2">
            <label for="filter-client" class="form-label">Client</label>
            <select id="filter-client" name="client" class="form-select form-select-sm">
                <option value="">Tous les clients</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @selected((string) $filters['client'] === (string) $client->id)>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-6 col-lg-1">
            <label for="filter-sort" class="form-label">Tri</label>
            <select id="filter-sort" name="sort" class="form-select form-select-sm">
                <option value="latest" @selected($filters['sort'] === 'latest')>Récentes</option>
                <option value="oldest" @selected($filters['sort'] === 'oldest')>Anciennes</option>
                <option value="priority" @selected($filters['sort'] === 'priority')>Priorité</option>
                <option value="due" @selected($filters['sort'] === 'due')>Échéance</option>
                <option value="title" @selected($filters['sort'] === 'title')>Titre</option>
            </select>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mt-3 pt-3 border-top">
        <div class="d-flex align-items-center gap-2">
            <label for="filter-project" class="form-label mb-0">Projet</label>
            <select id="filter-project" name="project" class="form-select form-select-sm" style="width:auto">
                <option value="">Tous</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected((string) $filters['project'] === (string) $project->id)>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <label for="filter-from" class="form-label mb-0">Créées du</label>
            <input type="date" id="filter-from" name="from" value="{{ $filters['from'] }}"
                   class="form-control form-control-sm" style="width:auto">
            <label for="filter-to" class="form-label mb-0">au</label>
            <input type="date" id="filter-to" name="to" value="{{ $filters['to'] }}"
                   class="form-control form-control-sm" style="width:auto">
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="filter-overdue" name="overdue" value="1"
                   @checked($filters['overdue'])>
            <label class="form-check-label tk-fs-sm" for="filter-overdue">En retard uniquement</label>
        </div>

        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Réinitialiser
            </a>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-funnel me-1"></i>Filtrer
            </button>
        </div>
    </div>
</form>
