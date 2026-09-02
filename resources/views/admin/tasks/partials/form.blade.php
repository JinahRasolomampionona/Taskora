@php
    // Estimations proposées, exprimées en minutes (journée de travail = 8 h).
    $estimateOptions = [
        15 => '15 min',
        30 => '30 min',
        60 => '1 h',
        120 => '2 h',
        240 => '4 h',
        480 => '1 jour',
        960 => '2 jours',
        1440 => '3 jours',
        2400 => '1 semaine',
    ];

    $isEdit = $task->exists;
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <x-panel title="Contenu de la tâche">
            <div class="tk-form-section">
                <div class="mb-3">
                    <label for="title" class="form-label tk-required">Titre</label>
                    <input type="text" id="title" name="title"
                           value="{{ old('title', $task->title) }}"
                           class="form-control @error('title') is-invalid @enderror"
                           placeholder="Ex. Refonte de la page de contact" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label tk-required">Description</label>
                    <textarea id="description" name="description" rows="6"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Contexte, objectif attendu, contraintes…" required>{{ old('description', $task->description) }}</textarea>
                    <div class="form-text">Visible par le client et par l'agent assigné.</div>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-0">
                    <label for="mission_details" class="form-label">Missions à effectuer</label>
                    <textarea id="mission_details" name="mission_details" rows="6"
                              class="form-control @error('mission_details') is-invalid @enderror"
                              placeholder="Étapes, consignes, liens utiles…">{{ old('mission_details', $task->mission_details) }}</textarea>
                    <div class="form-text">Cadrage opérationnel destiné à l'agent.</div>
                    @error('mission_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </x-panel>
    </div>

    <div class="col-lg-4">
        <x-panel title="Organisation" class="mb-3">
            <div class="mb-3">
                <span class="form-label d-block tk-required">Priorité</span>
                <div class="tk-choice">
                    @foreach (\App\Enums\TaskPriority::cases() as $priority)
                        <div class="tk-choice__item">
                            <input type="radio" id="priority-{{ $priority->value }}" name="priority"
                                   value="{{ $priority->value }}"
                                   @checked(old('priority', $task->priority?->value ?? 'normal') === $priority->value)>
                            <label for="priority-{{ $priority->value }}">
                                <i class="bi {{ $priority === \App\Enums\TaskPriority::Urgent ? 'bi-exclamation-triangle' : 'bi-circle' }}"></i>
                                {{ $priority->label() }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('priority')<div class="text-danger tk-fs-xs mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="client_id" class="form-label tk-required">Client</label>
                <select id="client_id" name="client_id"
                        class="form-select @error('client_id') is-invalid @enderror" required>
                    <option value="">Sélectionner un client…</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}"
                                @selected((string) old('client_id', $task->client_id) === (string) $client->id)>
                            {{ $client->name }}@if ($client->company) — {{ $client->company }}@endif
                        </option>
                    @endforeach
                </select>
                @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="assigned_to" class="form-label">Agent assigné</label>
                <select id="assigned_to" name="assigned_to"
                        class="form-select @error('assigned_to') is-invalid @enderror">
                    <option value="">Laisser en attente</option>
                    @foreach ($agents as $agent)
                        <option value="{{ $agent->id }}"
                                @selected((string) old('assigned_to', $task->assigned_to) === (string) $agent->id)>
                            {{ $agent->name }}@if ($agent->job_title) — {{ $agent->job_title }}@endif
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Assigner un agent bascule la tâche en « À faire ».</div>
                @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-0">
                <label for="project_id" class="form-label">Projet</label>
                <select id="project_id" name="project_id"
                        class="form-select @error('project_id') is-invalid @enderror">
                    <option value="">Aucun projet</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}"
                                @selected((string) old('project_id', $task->project_id) === (string) $project->id)>
                            {{ $project->code }} — {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-panel>

        <x-panel title="Planification">
            <div class="mb-3">
                <label for="estimated_minutes" class="form-label">Estimation</label>
                <select id="estimated_minutes" name="estimated_minutes"
                        class="form-select @error('estimated_minutes') is-invalid @enderror">
                    <option value="">Non estimée</option>
                    @foreach ($estimateOptions as $minutes => $label)
                        <option value="{{ $minutes }}"
                                @selected((string) old('estimated_minutes', $task->estimated_minutes) === (string) $minutes)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('estimated_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if ($isEdit)
                <div class="mb-3">
                    <label for="spent_minutes" class="form-label">Temps enregistré (minutes)</label>
                    <input type="number" id="spent_minutes" name="spent_minutes" min="0" step="5"
                           value="{{ old('spent_minutes', $task->spent_minutes) }}"
                           class="form-control @error('spent_minutes') is-invalid @enderror">
                    @error('spent_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endif

            <div class="mb-0">
                <label for="due_date" class="form-label">Date limite</label>
                <input type="date" id="due_date" name="due_date"
                       value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                       class="form-control @error('due_date') is-invalid @enderror">
                @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-panel>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
    <a href="{{ $isEdit ? route('admin.tasks.show', $task) : route('admin.tasks.index') }}"
       class="btn btn-light">Annuler</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Enregistrer les modifications' : 'Créer la tâche' }}
    </button>
</div>
