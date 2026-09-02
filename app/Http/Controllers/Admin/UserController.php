<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Liste des comptes.
     *
     * Le parametre de route optionnel `role` permet de servir les ecrans
     * "Agents" et "Clients" avec le meme controleur.
     */
    public function index(Request $request, ?string $role = null): View
    {
        Gate::authorize('viewAny', User::class);

        $roleFilter = UserRole::tryFrom((string) ($role ?? $request->query('role')));

        $users = User::query()
            ->when($roleFilter, fn ($q) => $q->where('role', $roleFilter->value))
            ->when($request->filled('q'), function ($q) use ($request) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], trim($request->query('q'))).'%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('company', 'like', $like));
            })
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->withCount([
                'assignedTasks as active_tasks_count' => fn ($q) => $q->whereIn('status', TaskStatus::activeStatuses()),
                'tasksAsClient as requests_count',
            ])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roleFilter' => $roleFilter,
            'filters' => [
                'q' => $request->query('q', ''),
                'active' => $request->query('active', ''),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', User::class);

        return view('admin.users.create', [
            'user' => new User(['role' => UserRole::tryFrom((string) $request->query('role')) ?? UserRole::Agent, 'is_active' => true]),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Le compte de '.$user->name.' a ete cree.');
    }

    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        $user->loadCount([
            'assignedTasks as active_tasks_count' => fn ($q) => $q->whereIn('status', TaskStatus::activeStatuses()),
            'assignedTasks as completed_tasks_count' => fn ($q) => $q->where('status', TaskStatus::Completed->value),
            'tasksAsClient as requests_count',
        ]);

        $recentTasks = ($user->isClient() ? $user->tasksAsClient() : $user->assignedTasks())
            ->with(['client:id,name', 'agent:id,name'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('admin.users.show', compact('user', 'recentTasks'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Le compte de '.$user->name.' a ete mis a jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Le compte de '.$name.' a ete supprime.');
    }
}
