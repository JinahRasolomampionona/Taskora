<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Taskora — plateforme de gestion et de suivi des tâches.">

    <title>@yield('title', 'Tableau de bord') · Taskora</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="tk-app tk-shell" data-tk-shell>
        @include('layouts.partials.sidebar')

        <div class="tk-backdrop" data-tk-backdrop></div>

        <div class="tk-main">
            @include('layouts.partials.topbar')

            <main class="tk-page">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Les messages flash sont convertis en toasts par notifications.js --}}
    @foreach (['success', 'error', 'warning', 'info'] as $flash)
        @if (session($flash))
            <span data-tk-flash="{{ $flash }}" data-tk-flash-message="{{ session($flash) }}" hidden></span>
        @endif
    @endforeach

    @if ($errors->any() && ! session('error'))
        <span data-tk-flash="error"
              data-tk-flash-message="{{ $errors->count() }} champ(s) à corriger dans le formulaire."
              hidden></span>
    @endif

    @stack('scripts')
</body>
</html>
