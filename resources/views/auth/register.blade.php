@extends('layouts.auth')

@section('title', 'Créer un compte')

@section('content')
    <a href="{{ route('login') }}" class="tk-auth__brand d-lg-none">
        <span class="tk-sidebar__mark">Tk</span>
        <span class="tk-sidebar__name">Taskora</span>
    </a>

    <h1 class="h4 mb-1" style="font-weight:680; letter-spacing:-.02em;">Créer un compte</h1>
    <p class="tk-text-muted tk-fs-sm mb-4">
        Choisissez le type de compte à créer : chaque rôle ouvre son propre espace de travail.
    </p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 tk-fs-sm" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" data-tk-role-form novalidate>
        @csrf

        <fieldset class="mb-4">
            <legend class="form-label tk-required">Type de compte</legend>

            <div class="tk-role-picker">
                @foreach ($roles as $role)
                    <label class="tk-role-option" for="role_{{ $role->value }}">
                        <input class="form-check-input" type="radio" name="role" id="role_{{ $role->value }}"
                               value="{{ $role->value }}" data-tk-role-input
                               @checked($selectedRole === $role) required>
                        <span class="tk-role-option__body">
                            <span class="tk-role-option__title">
                                <i class="bi {{ $role->icon() }}"></i>{{ $role->label() }}
                            </span>
                            <span class="tk-role-option__desc">{{ $role->description() }}</span>
                        </span>
                    </label>
                @endforeach
            </div>

            @error('role')<div class="text-danger tk-fs-xs mt-2">{{ $message }}</div>@enderror
        </fieldset>

        <div class="mb-3">
            <label for="name" class="form-label tk-required">Nom complet</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Camille Durand" autocomplete="name" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label tk-required">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="contact@entreprise.fr" autocomplete="username" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Champs propres aux comptes internes (administrateur, agent). --}}
        <div class="mb-3" data-tk-role-field="admin user">
            <label for="job_title" class="form-label">Fonction</label>
            <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}"
                   class="form-control @error('job_title') is-invalid @enderror"
                   placeholder="Responsable support" autocomplete="organization-title">
            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Champs propres aux comptes clients. --}}
        <div class="row g-3 mb-3" data-tk-role-field="client">
            <div class="col-sm-7">
                <label for="company" class="form-label">Société</label>
                <input type="text" id="company" name="company" value="{{ old('company') }}"
                       class="form-control @error('company') is-invalid @enderror"
                       placeholder="Nord Bâtiment" autocomplete="organization">
                @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-5">
                <label for="phone" class="form-label">Téléphone</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                       class="form-control @error('phone') is-invalid @enderror"
                       placeholder="06 12 34 56 78" autocomplete="tel">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <x-password-field name="password" label="Mot de passe" required
                              hint="8 caractères minimum, avec au moins une lettre et un chiffre." />
        </div>

        <div class="mb-4">
            <x-password-field name="password_confirmation" label="Confirmer le mot de passe" required />
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="bi bi-person-plus me-1"></i>Créer mon compte
        </button>

        <p class="text-center tk-fs-sm tk-text-muted mb-0">
            Vous avez déjà un compte ?
            <a href="{{ route('login') }}" class="fw-semibold">Se connecter</a>
        </p>
    </form>
@endsection
