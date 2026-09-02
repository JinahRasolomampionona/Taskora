<div class="tk-sidebar__section">Pilotage</div>

<a href="{{ route('admin.dashboard') }}"
   class="tk-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
    <i class="bi bi-grid-1x2"></i>
    <span>Tableau de bord</span>
</a>

<a href="{{ route('admin.tasks.board') }}"
   class="tk-nav-link {{ request()->routeIs('admin.tasks.board') ? 'is-active' : '' }}">
    <i class="bi bi-kanban"></i>
    <span>Tableau Kanban</span>
</a>

<div class="tk-sidebar__section">Gestion</div>

<a href="{{ route('admin.tasks.index') }}"
   class="tk-nav-link {{ request()->routeIs('admin.tasks.index') || request()->routeIs('admin.tasks.show') || request()->routeIs('admin.tasks.create') || request()->routeIs('admin.tasks.edit') ? 'is-active' : '' }}">
    <i class="bi bi-check2-square"></i>
    <span>Tâches</span>
    @if (($navCounts['active'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['active'] }}</span>
    @endif
</a>

<a href="{{ route('admin.projects.index') }}"
   class="tk-nav-link {{ request()->routeIs('admin.projects.*') ? 'is-active' : '' }}">
    <i class="bi bi-folder2"></i>
    <span>Projets</span>
</a>

<a href="{{ route('admin.agents.index') }}"
   class="tk-nav-link {{ request()->routeIs('admin.agents.index') ? 'is-active' : '' }}">
    <i class="bi bi-person-badge"></i>
    <span>Agents</span>
    @if (($navCounts['agents'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['agents'] }}</span>
    @endif
</a>

<a href="{{ route('admin.clients.index') }}"
   class="tk-nav-link {{ request()->routeIs('admin.clients.index') ? 'is-active' : '' }}">
    <i class="bi bi-people"></i>
    <span>Clients</span>
    @if (($navCounts['clients'] ?? 0) > 0)
        <span class="tk-nav-link__count">{{ $navCounts['clients'] }}</span>
    @endif
</a>

<a href="{{ route('admin.users.index') }}"
   class="tk-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
    <i class="bi bi-shield-lock"></i>
    <span>Utilisateurs</span>
</a>

<div class="tk-sidebar__section">Suivi</div>

<a href="{{ route('admin.archives.index') }}"
   class="tk-nav-link {{ request()->routeIs('admin.archives.*') ? 'is-active' : '' }}">
    <i class="bi bi-archive"></i>
    <span>Archives</span>
</a>

<a href="{{ route('profile.edit') }}"
   class="tk-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
    <i class="bi bi-sliders"></i>
    <span>Paramètres</span>
</a>
