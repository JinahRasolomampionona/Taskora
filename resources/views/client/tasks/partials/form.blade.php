@php
    $isEdit = $task->exists;
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <x-panel title="Votre demande">
            <div class="mb-3">
                <label for="title" class="form-label tk-required">Titre de la demande</label>
                <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}"
                       class="form-control @error('title') is-invalid @enderror"
                       placeholder="Ex. Mettre à jour les tarifs sur le site" required autofocus>
                <div class="form-text">Un titre court et explicite aide à traiter votre demande plus vite.</div>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-0">
                <label for="description" class="form-label tk-required">Description détaillée</label>
                <textarea id="description" name="description" rows="9"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Décrivez le besoin, le contexte, les éléments à fournir et le résultat attendu…"
                          required>{{ old('description', $task->description) }}</textarea>
                <div class="form-text">Plus la description est précise, moins il y aura d'allers-retours.</div>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-panel>
    </div>

    <div class="col-lg-4">
        <x-panel title="Priorité et délai">
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
                <div class="form-text">Réservez « Urgent » aux demandes réellement bloquantes.</div>
                @error('priority')<div class="text-danger tk-fs-xs mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Date souhaitée</label>
                <input type="date" id="due_date" name="due_date"
                       value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                       min="{{ now()->format('Y-m-d') }}"
                       class="form-control @error('due_date') is-invalid @enderror">
                @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-0">
                <label for="project_id" class="form-label">Projet concerné</label>
                <select id="project_id" name="project_id"
                        class="form-select @error('project_id') is-invalid @enderror">
                    <option value="">Aucun / je ne sais pas</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}"
                                @selected((string) old('project_id', $task->project_id) === (string) $project->id)>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-panel>

        <div class="alert alert-light border mt-3 tk-fs-sm mb-0" role="note">
            <i class="bi bi-info-circle me-1 text-primary"></i>
            L'assignation, l'estimation et le suivi interne sont pris en charge par nos équipes.
            Vous serez tenu informé de chaque changement de statut.
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
    <a href="{{ $isEdit ? route('client.tasks.show', $task) : route('client.tasks.index') }}"
       class="btn btn-light">Annuler</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-send me-1"></i>{{ $isEdit ? 'Enregistrer' : 'Envoyer la demande' }}
    </button>
</div>
