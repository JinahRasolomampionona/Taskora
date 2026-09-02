<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Agent;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskStatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->homeRoute())
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'create'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');

    Route::get('/inscription', [RegisterController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/deconnexion', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Routes authentifiees communes
|--------------------------------------------------------------------------
| Les actions ci-dessous sont partagees par les trois espaces : le perimetre
| reel est verifie par TaskPolicy, jamais par le prefixe d'URL.
*/

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::patch('/taches/{task}/statut', [TaskStatusController::class, 'update'])->name('tasks.status');
    Route::patch('/taches/{task}/assignation', [TaskAssignmentController::class, 'update'])->name('tasks.assign');
    Route::post('/taches/{task}/commentaires', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
});

/*
|--------------------------------------------------------------------------
| Espace administrateur
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('/tableau', [Admin\TaskController::class, 'board'])->name('tasks.board');
        Route::resource('tasks', Admin\TaskController::class);

        Route::resource('projects', Admin\ProjectController::class);

        Route::get('/agents', [Admin\UserController::class, 'index'])->defaults('role', 'user')->name('agents.index');
        Route::get('/clients', [Admin\UserController::class, 'index'])->defaults('role', 'client')->name('clients.index');
        Route::resource('users', Admin\UserController::class);

        Route::get('/archives', [Admin\ArchiveController::class, 'index'])->name('archives.index');
    });

/*
|--------------------------------------------------------------------------
| Espace agent
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])
    ->prefix('agent')
    ->name('agent.')
    ->group(function () {
        Route::get('/', Agent\DashboardController::class)->name('dashboard');
        Route::get('/tableau', [Agent\TaskController::class, 'board'])->name('tasks.board');
        Route::get('/historique', [Agent\TaskController::class, 'history'])->name('tasks.history');
        Route::get('/taches', [Agent\TaskController::class, 'index'])->name('tasks.index');
        Route::get('/taches/{task}', [Agent\TaskController::class, 'show'])->name('tasks.show');
    });

/*
|--------------------------------------------------------------------------
| Espace client
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/', Client\DashboardController::class)->name('dashboard');
        Route::get('/demandes', [Client\TaskController::class, 'index'])->name('tasks.index');
        Route::get('/demandes/nouvelle', [Client\TaskController::class, 'create'])->name('tasks.create');
        Route::post('/demandes', [Client\TaskController::class, 'store'])->name('tasks.store');
        Route::get('/demandes/{task}', [Client\TaskController::class, 'show'])->name('tasks.show');
        Route::get('/demandes/{task}/modifier', [Client\TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/demandes/{task}', [Client\TaskController::class, 'update'])->name('tasks.update');
    });
