@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
    <a href="{{ route('login') }}" class="tk-auth__brand d-lg-none">
        <span class="tk-sidebar__mark">Tk</span>
        <span class="tk-sidebar__name">Taskora</span>
    </a>

    <h1 class="h4 mb-1" style="font-weight:680; letter-spacing:-.02em;">Connexion</h1>
    <p class="tk-text-muted tk-fs-sm mb-4">Accédez à votre espace de travail Taskora.</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 tk-fs-sm" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" data-tk-login-form novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label tk-required">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="prenom.nom@entreprise.fr"
                   autocomplete="username" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <x-password-field name="password" label="Mot de passe"
                              autocomplete="current-password" required />
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1"
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label tk-fs-sm" for="remember">Rester connecté sur cet appareil</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
        </button>
    </form>

    @php
        // Client et agent en premier : ce sont les creations courantes depuis l'ecran de connexion.
        $signupRoles = collect(\App\Enums\UserRole::registrable())
            ->sortBy(fn ($role) => match ($role) {
                \App\Enums\UserRole::Client => 0,
                \App\Enums\UserRole::Agent => 1,
                default => 2,
            })
            ->values();
    @endphp

    @if ($signupRoles->isNotEmpty())
        <div class="tk-auth__signup">
            <div class="tk-auth__signup-label">Créer un compte</div>
            <div class="tk-auth__signup-links">
                @foreach ($signupRoles as $role)
                    <a href="{{ route('register', ['role' => $role->value]) }}">
                        <i class="bi {{ $role->icon() }}"></i>{{ $role->label() }}
                    </a>
                @endforeach
            </div>
            <p class="tk-fs-xs tk-text-muted mt-2 mb-0">
                Chaque lien ouvre le formulaire d'inscription avec le type de compte présélectionné.
            </p>
        </div>
    @endif


    @if (! app()->isProduction())
        <div class="tk-demo-accounts">
            <div class="fw-semibold text-dark mb-2">
                <i class="bi bi-info-circle me-1"></i>Comptes de démonstration (environnement local)
            </div>
            <div class="d-grid gap-1">
                @foreach ([
                    ['Administrateur', 'admin@taskora.test'],
                    ['Agent', 'lea.moreau@taskora.test'],
                    ['Client', 'contact@nordbat.fr'],
                ] as [$roleLabel, $email])
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <span>{{ $roleLabel }} · <code>{{ $email }}</code></span>
                        <button type="button"
                                data-tk-fill-login="{{ $email }}"
                                data-tk-fill-password="password123">Utiliser</button>
                    </div>
                @endforeach
            </div>
            <div class="mt-2">Mot de passe commun : <code>password123</code></div>
        </div>
    @endif
@endsection
