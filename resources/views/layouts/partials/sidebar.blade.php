@php
    $user = auth()->user();
@endphp

<aside class="tk-sidebar">
    <a href="{{ route($user->homeRoute()) }}" class="tk-sidebar__brand">
        <span class="tk-sidebar__mark">Tk</span>
        <span class="tk-sidebar__name">Taskora</span>
    </a>

    <nav class="tk-sidebar__nav" aria-label="Navigation principale">
        @if ($user->isAdmin())
            @include('layouts.partials.nav-admin')
        @elseif ($user->isAgent())
            @include('layouts.partials.nav-agent')
        @else
            @include('layouts.partials.nav-client')
        @endif
    </nav>

    <div class="tk-sidebar__footer">
        <a href="{{ route('profile.edit') }}" class="tk-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
            <x-avatar :user="$user" size="sm" />
            <span class="tk-user-chip__meta flex-grow-1 min-w-0">
                <span class="tk-user-chip__name">{{ $user->name }}</span>
                <span class="tk-user-chip__role">{{ $user->role->label() }}</span>
            </span>
        </a>
    </div>
</aside>
