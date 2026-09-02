@php
    $isEdit = $user->exists;
    $canChangeRole = ! $isEdit || auth()->user()->can('changeRole', $user);
@endphp

<div class="row g-3">
    <div class="col-lg-8">
        <x-panel title="Identité">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label tk-required">Nom complet</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Léa Moreau" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label tk-required">Adresse e-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="lea.moreau@taskora.test" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="job_title" class="form-label">Fonction</label>
                    <input type="text" id="job_title" name="job_title" value="{{ old('job_title', $user->job_title) }}"
                           class="form-control @error('job_title') is-invalid @enderror"
                           placeholder="Développeuse back-end">
                    @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="company" class="form-label">Société</label>
                    <input type="text" id="company" name="company" value="{{ old('company', $user->company) }}"
                           class="form-control @error('company') is-invalid @enderror"
                           placeholder="Nord Bâtiment">
                    @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="06 12 34 56 78">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </x-panel>
    </div>

    <div class="col-lg-4">
        <x-panel title="Accès" class="mb-3">
            <div class="mb-3">
                <label for="role" class="form-label {{ $canChangeRole ? 'tk-required' : '' }}">Rôle</label>

                @if ($canChangeRole)
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach (\App\Enums\UserRole::options() as $value => $label)
                            <option value="{{ $value }}"
                                    @selected(old('role', $user->role?->value ?? 'user') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        L'agent traite les tâches, le client dépose les demandes,
                        l'administrateur supervise l'ensemble.
                    </div>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @else
                    <div><x-role-badge :role="$user->role" /></div>
                    <div class="form-text">Vous ne pouvez pas modifier votre propre rôle.</div>
                @endif
            </div>

            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       @checked(old('is_active', $user->is_active ?? true))>
                <label class="form-check-label tk-fs-sm" for="is_active">Compte actif</label>
                <div class="form-text">Un compte désactivé ne peut plus se connecter.</div>
            </div>
        </x-panel>

        <x-panel title="Mot de passe">
            @php
                $passwordHint = $isEdit
                    ? 'Laissez vide pour conserver le mot de passe actuel.'
                    : '8 caractères minimum, lettres et chiffres.';
            @endphp

            <div class="mb-3">
                <x-password-field name="password" :required="! $isEdit" :hint="$passwordHint"
                                  :label="$isEdit ? 'Nouveau mot de passe' : 'Mot de passe'" />
            </div>

            <div class="mb-0">
                <x-password-field name="password_confirmation" label="Confirmation" />
            </div>
        </x-panel>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
    <a href="{{ $isEdit ? route('admin.users.show', $user) : route('admin.users.index') }}"
       class="btn btn-light">Annuler</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2 me-1"></i>{{ $isEdit ? 'Enregistrer' : 'Créer le compte' }}
    </button>
</div>
