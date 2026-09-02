<div class="tk-sidebar__section">Mon espace</div>

<a href="{{ route('client.dashboard') }}"
   class="tk-nav-link {{ request()->routeIs('client.dashboard') ? 'is-active' : '' }}">
    <i class="bi bi-grid-1x2"></i>
    <span>Tableau de bord</span>
</a>

<a href="{{ route('client.tasks.create') }}"
   class="tk-nav-link {{ request()->routeIs('client.tasks.create') ? 'is-active' : '' }}">
    <i class="bi bi-plus-square"></i>
    <span>Nouvelle demande</span>
</a>

<a href="{{ route('client.tasks.index') }}"
   class="tk-nav-link {{ request()->routeIs('client.tasks.index') || request()->routeIs('client.tasks.show') ? 'is-active' : '' }}">
    <i class="bi bi-inbox"></i>
    <span>Mes demandes</span>
    @if (($navCounts['total'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['total'] }}</span>
    @endif
</a>

<div class="tk-sidebar__section">Suivi</div>

<a href="{{ route('client.tasks.index', ['status' => 'pending']) }}"
   class="tk-nav-link {{ request()->routeIs('client.tasks.index') && request('status') === 'pending' ? 'is-active' : '' }}">
    <i class="bi bi-hourglass-split"></i>
    <span>En attente</span>
    @if (($navCounts['pending'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['pending'] }}</span>
    @endif
</a>

<a href="{{ route('client.tasks.index', ['status' => 'in_progress']) }}"
   class="tk-nav-link {{ request()->routeIs('client.tasks.index') && request('status') === 'in_progress' ? 'is-active' : '' }}">
    <i class="bi bi-play-circle"></i>
    <span>En cours</span>
    @if (($navCounts['in_progress'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['in_progress'] }}</span>
    @endif
</a>

<a href="{{ route('client.tasks.index', ['status' => 'completed']) }}"
   class="tk-nav-link {{ request()->routeIs('client.tasks.index') && request('status') === 'completed' ? 'is-active' : '' }}">
    <i class="bi bi-check-circle"></i>
    <span>Terminées</span>
    @if (($navCounts['completed'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['completed'] }}</span>
    @endif
</a>

<div class="tk-sidebar__section">Compte</div>

<a href="{{ route('profile.edit') }}"
   class="tk-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
    <i class="bi bi-sliders"></i>
    <span>Mon profil</span>
</a>
