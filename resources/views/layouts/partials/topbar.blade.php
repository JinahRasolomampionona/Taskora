@php
    $user = auth()->user();
    $alerts = $navCounts['alerts'] ?? 0;

    $searchRoute = match (true) {
        $user->isAdmin() => route('admin.tasks.index'),
        $user->isAgent() => route('agent.tasks.index'),
        default => route('client.tasks.index'),
    };
@endphp

<header class="tk-topbar">
    <button type="button" class="tk-topbar__toggle" data-tk-sidebar-toggle aria-label="Afficher ou réduire le menu">
        <i class="bi bi-list"></i>
    </button>

    <form action="{{ $searchRoute }}" method="GET" class="tk-topbar__search d-none d-md-block" role="search">
        <div class="position-relative">
            <i class="bi bi-search position-absolute tk-text-faint"
               style="left:.7rem; top:50%; transform:translateY(-50%); font-size:.875rem;"></i>
            <input type="search" name="q" value="{{ request('q') }}"
                   class="form-control form-control-sm ps-4"
                   placeholder="Rechercher une tâche, un code…"
                   aria-label="Rechercher une tâche">
        </div>
    </form>

    <div class="ms-auto d-flex align-items-center gap-1">
        @if ($user->isClient())
            <a href="{{ route('client.tasks.create') }}" class="btn btn-primary btn-sm d-none d-sm-inline-flex">
                <i class="bi bi-plus-lg me-1"></i>Nouvelle demande
            </a>
        @elseif ($user->isAdmin())
            <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm d-none d-sm-inline-flex">
                <i class="bi bi-plus-lg me-1"></i>Nouvelle tâche
            </a>
        @endif

        <div class="dropdown">
            <button class="tk-icon-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" aria-label="Notifications">
                <i class="bi bi-bell"></i>
                @if ($alerts > 0)
                    <span class="tk-icon-btn__dot">{{ $alerts > 99 ? '99+' : $alerts }}</span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end p-0" style="width:290px;">
                <div class="px-3 py-2 border-bottom">
                    <div class="fw-semibold tk-fs-sm">Notifications</div>
                </div>

                <div class="p-2">
                    @include('layouts.partials.notifications-list')
                </div>
            </div>
        </div>

        <div class="dropdown">
            <button class="btn btn-link p-1 border-0 d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <x-avatar :user="$user" />
                <span class="d-none d-lg-block text-start lh-1">
                    <span class="d-block tk-fs-sm fw-semibold text-dark">{{ Str::limit($user->name, 18) }}</span>
                    <span class="d-block tk-fs-xs tk-text-muted">{{ $user->role->label() }}</span>
                </span>
                <i class="bi bi-chevron-down tk-fs-xs tk-text-faint d-none d-lg-block"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end" style="min-width:210px;">
                <li class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-semibold tk-fs-sm">{{ $user->name }}</div>
                    <div class="tk-fs-xs tk-text-muted text-truncate">{{ $user->email }}</div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i>Mon profil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route($user->homeRoute()) }}">
                        <i class="bi bi-grid-1x2 me-2"></i>Tableau de bord
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Se déconnecter
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
