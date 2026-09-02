<div class="tk-sidebar__section">Mon espace</div>

<a href="{{ route('agent.dashboard') }}"
   class="tk-nav-link {{ request()->routeIs('agent.dashboard') ? 'is-active' : '' }}">
    <i class="bi bi-grid-1x2"></i>
    <span>Tableau de bord</span>
</a>

<a href="{{ route('agent.tasks.index') }}"
   class="tk-nav-link {{ request()->routeIs('agent.tasks.index') || request()->routeIs('agent.tasks.show') ? 'is-active' : '' }}">
    <i class="bi bi-check2-square"></i>
    <span>Mes tâches</span>
    @if (($navCounts['active'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['active'] }}</span>
    @endif
</a>

<a href="{{ route('agent.tasks.board') }}"
   class="tk-nav-link {{ request()->routeIs('agent.tasks.board') ? 'is-active' : '' }}">
    <i class="bi bi-kanban"></i>
    <span>Tableau Kanban</span>
</a>

<div class="tk-sidebar__section">Travail</div>

<a href="{{ route('agent.tasks.index', ['status' => 'todo']) }}"
   class="tk-nav-link {{ request()->routeIs('agent.tasks.index') && request('status') === 'todo' ? 'is-active' : '' }}">
    <i class="bi bi-list-check"></i>
    <span>À faire</span>
    @if (($navCounts['todo'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['todo'] }}</span>
    @endif
</a>

<a href="{{ route('agent.tasks.index', ['status' => 'in_progress']) }}"
   class="tk-nav-link {{ request()->routeIs('agent.tasks.index') && request('status') === 'in_progress' ? 'is-active' : '' }}">
    <i class="bi bi-play-circle"></i>
    <span>En cours</span>
    @if (($navCounts['in_progress'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['in_progress'] }}</span>
    @endif
</a>

<a href="{{ route('agent.tasks.history') }}"
   class="tk-nav-link {{ request()->routeIs('agent.tasks.history') ? 'is-active' : '' }}">
    <i class="bi bi-clock-history"></i>
    <span>Historique</span>
</a>

<div class="tk-sidebar__section">Compte</div>

<a href="{{ route('profile.edit') }}"
   class="tk-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
    <i class="bi bi-sliders"></i>
    <span>Mon profil</span>
</a>
