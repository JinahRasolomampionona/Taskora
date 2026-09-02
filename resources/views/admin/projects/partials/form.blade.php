@php
    $isEdit = $project->exists;
    $selectedMembers = old('members', $isEdit ? $project->members->pluck('id')->all() : []);
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <x-panel title="Informations du projet">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="code" class="form-label tk-required">Code</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $project->code) }}"
                           class="form-control @error('code') is-invalid @enderror"
                           placeholder="NORDBAT" maxlength="20" required>
                    <div class="form-text">Identifiant court, lettres et chiffres.</div>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-8">
                    <label for="name" class="form-label tk-required">Nom du projet</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Équipe Nord Bâtiment" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Périmètre, objectifs, interlocuteurs…">{{ old('description', $project->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </x-panel>
    </div>

    <div class="col-lg-4">
        <x-panel title="Paramètres" class="mb-3">
            <div class="mb-3">
                <label for="owner_id" class="form-label">Propriétaire</label>
                <select id="owner_id" name="owner_id" class="form-select @error('owner_id') is-invalid @enderror">
                    <option value="">Non défini</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                                @selected((string) old('owner_id', $project->owner_id) === (string) $user->id)>
                            {{ $user->name }} — {{ $user->role->label() }}
                        </option>
                    @endforeach
                </select>
                @error('owner_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="color" class="form-label">Couleur</label>
                <input type="color" id="color" name="color"
                       value="{{ old('color', $project->color ?? '#2563eb') }}"
                       class="form-control form-control-color @error('color') is-invalid @enderror">
                @error('color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       @checked(old('is_active', $project->is_active ?? true))>
                <label class="form-check-label tk-fs-sm" for="is_active">Projet actif</label>
            </div>
        </x-panel>

        <x-panel title="Équipe">
            @if ($users->isEmpty())
                <x-empty-state icon="bi-people" title="Aucun collaborateur disponible" small />
            @else
                <div class="d-grid gap-2" style="max-height:280px; overflow-y:auto">
                    @foreach ($users as $user)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="members[]"
                                   value="{{ $user->id }}" id="member-{{ $user->id }}"
                                   @checked(in_array($user->id, array_map('intval', (array) $selectedMembers), true))>
                            <label class="form-check-label d-flex align-items-center gap-2" for="member-{{ $user->id }}">
                                <x-avatar :user="$user" size="sm" />
                                <span class="tk-fs-sm">{{ $user->name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-panel>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
    <a href="{{ $isEdit ? route('admin.projects.show', $project) : route('admin.projects.index') }}"
       class="btn btn-light">Annuler</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Enregistrer' : 'Créer le projet' }}
    </button>
</div>
