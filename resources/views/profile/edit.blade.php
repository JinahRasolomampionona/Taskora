@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <x-page-header title="Mon profil"
                   subtitle="Vos informations personnelles et vos identifiants de connexion."
                   :breadcrumbs="['Taskora' => route(auth()->user()->homeRoute()), 'Profil' => null]" />

    <div class="row g-3">
        <div class="col-lg-4">
            <x-panel title="Compte">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <x-avatar :user="$user" size="xl" />
                    <div class="min-w-0">
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <x-role-badge :role="$user->role" />
                    </div>
                </div>

                <div class="tk-meta-list">
                    <div>
                        <div class="tk-meta-item__label">Adresse e-mail</div>
                        <div class="tk-meta-item__value text-break">{{ $user->email }}</div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">Membre depuis</div>
                        <div class="tk-meta-item__value">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="tk-meta-item__label">Dernière connexion</div>
                        <div class="tk-meta-item__value">
                            {{ $user->last_login_at?->format('d/m/Y à H:i') ?? '—' }}
                        </div>
                    </div>
                </div>
            </x-panel>
        </div>

        <div class="col-lg-8">
            <x-panel title="Informations personnelles" class="mb-3">
                <form method="POST" action="{{ route('profile.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label tk-required">Nom complet</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label tk-required">Adresse e-mail</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="job_title" class="form-label">Fonction</label>
                            <input type="text" id="job_title" name="job_title"
                                   value="{{ old('job_title', $user->job_title) }}"
                                   class="form-control @error('job_title') is-invalid @enderror">
                            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="company" class="form-label">Société</label>
                            <input type="text" id="company" name="company"
                                   value="{{ old('company', $user->company) }}"
                                   class="form-control @error('company') is-invalid @enderror">
                            @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2 me-1"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </x-panel>

            <x-panel title="Mot de passe">
                <form method="POST" action="{{ route('profile.password') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <x-password-field name="current_password" label="Mot de passe actuel"
                                              autocomplete="current-password" required />
                        </div>

                        <div class="col-md-4">
                            <x-password-field name="password" label="Nouveau mot de passe" required />
                        </div>

                        <div class="col-md-4">
                            <x-password-field name="password_confirmation" label="Confirmation" required />
                        </div>
                    </div>

                    <div class="form-text mt-2">8 caractères minimum, avec au moins une lettre et un chiffre.</div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-shield-lock me-1"></i>Modifier le mot de passe
                        </button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>
@endsection
