<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use App\View\Composers\NavigationComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton : la sidebar et la barre superieure partagent le meme comptage.
        $this->app->singleton(NavigationComposer::class);
    }

    public function boot(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);

        // Le composer est partage : une seule instance, donc un seul comptage.
        View::composer(
            ['layouts.partials.sidebar', 'layouts.partials.topbar'],
            NavigationComposer::class
        );

        Paginator::useBootstrapFive();

        // Toute mise a jour non prevue par $fillable doit echouer bruyamment
        // en developpement plutot que passer inapercue.
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
    }
}
