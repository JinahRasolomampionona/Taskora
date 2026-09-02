<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $projects = Project::query()
            ->with('owner:id,name,role')
            ->withCount([
                'tasks',
                'tasks as open_tasks_count' => fn ($q) => $q->whereIn('status', TaskStatus::activeStatuses()),
                'members',
            ])
            ->when($request->filled('q'), function ($q) use ($request) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], trim($request->query('q'))).'%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $like)->orWhere('code', 'like', $like));
            })
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy('name')
            ->paginate((int) $request->query('per_page', 10))
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'filters' => [
                'q' => $request->query('q', ''),
                'active' => $request->query('active', ''),
                'per_page' => (int) $request->query('per_page', 10),
            ],
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('admin.projects.create', [
            'project' => new Project(['color' => '#2563eb', 'is_active' => true]),
            'users' => $this->assignableUsers(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $members = $data['members'] ?? [];
        unset($data['members']);
        $data['is_active'] = $request->boolean('is_active', true);

        $project = Project::create($data);
        $project->members()->sync($members);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Le projet '.$project->name.' a ete cree.');
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $project->load(['owner:id,name,role', 'members:id,name,role,job_title'])
            ->loadCount([
                'tasks',
                'tasks as open_tasks_count' => fn ($q) => $q->whereIn('status', TaskStatus::activeStatuses()),
            ]);

        $tasks = $project->tasks()
            ->with(['client:id,name', 'agent:id,name'])
            ->latest('created_at')
            ->paginate(10);

        return view('admin.projects.show', compact('project', 'tasks'));
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        $project->load('members:id');

        return view('admin.projects.edit', [
            'project' => $project,
            'users' => $this->assignableUsers(),
        ]);
    }

    public function update(StoreProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $members = $data['members'] ?? [];
        unset($data['members']);
        $data['is_active'] = $request->boolean('is_active');

        $project->update($data);
        $project->members()->sync($members);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Le projet '.$project->name.' a ete mis a jour.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $name = $project->name;
        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Le projet '.$name.' a ete supprime.');
    }

    private function assignableUsers()
    {
        return User::query()
            ->active()
            ->whereIn('role', ['admin', 'user'])
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'job_title']);
    }
}
