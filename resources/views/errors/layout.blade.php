<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · Taskora</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss'])
</head>
<body>
    <div class="tk-error">
        <div>
            <a href="{{ url('/') }}" class="tk-auth__brand mb-4">
                <span class="tk-sidebar__mark">Tk</span>
                <span class="tk-sidebar__name">Taskora</span>
            </a>

            <div class="tk-error__code">@yield('code')</div>
            <h1 class="tk-error__title">@yield('heading')</h1>
            <p class="tk-error__text">@yield('message')</p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ auth()->check() ? route(auth()->user()->homeRoute()) : url('/') }}"
                   class="btn btn-primary">
                    <i class="bi bi-house me-1"></i>Retour à l'accueil
                </a>
                <button type="button" class="btn btn-light" onclick="history.back()">
                    <i class="bi bi-arrow-left me-1"></i>Page précédente
                </button>
            </div>
        </div>
    </div>
</body>
</html>
