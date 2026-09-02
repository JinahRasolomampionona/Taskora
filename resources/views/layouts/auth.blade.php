<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Connexion') · Taskora</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="tk-auth">
        <aside class="tk-auth__aside">
            <div>
                <div class="d-flex align-items-center gap-2 mb-5">
                    <span class="tk-sidebar__mark">Tk</span>
                    <span class="tk-sidebar__name text-white">Taskora</span>
                </div>

                <h1 class="tk-auth__headline">Le travail de votre équipe, enfin sous contrôle.</h1>
                <p class="tk-auth__lead">
                    Centralisez les demandes de vos clients, distribuez-les à vos agents
                    et suivez chaque tâche de la prise en charge à l'archivage.
                </p>

                <ul class="tk-auth__points">
                    <li><i class="bi bi-kanban"></i><span>Tableau Kanban en six étapes, du dépôt de la demande à l'archive</span></li>
                    <li><i class="bi bi-shield-check"></i><span>Trois espaces cloisonnés : administration, agents, clients</span></li>
                    <li><i class="bi bi-lightning-charge"></i><span>Changements de statut instantanés, sans rechargement</span></li>
                    <li><i class="bi bi-clock-history"></i><span>Historique complet et traçabilité de chaque intervention</span></li>
                </ul>
            </div>

            <p class="tk-fs-xs mb-0" style="color:rgba(255,255,255,.55)">
                &copy; {{ date('Y') }} Taskora — plateforme interne de gestion des tâches.
            </p>
        </aside>

        <main class="tk-auth__form">
            <div class="tk-auth__card">
                @yield('content')
            </div>
        </main>
    </div>

    @foreach (['success', 'error', 'warning', 'info'] as $flash)
        @if (session($flash))
            <span data-tk-flash="{{ $flash }}" data-tk-flash-message="{{ session($flash) }}" hidden></span>
        @endif
    @endforeach
</body>
</html>
