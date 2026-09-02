<?php

namespace App\Http\Controllers\Client;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\FiltersTasks;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskStatistics;
use App\Services\TaskWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Espace client : creation et suivi des demandes.
 *
 * Le client ne choisit ni l'agent, ni l'estimation, ni le statut : ces champs
 * relevent de l'organisation interne et sont ignores ici.
 */
class TaskController extends Controller
{
    use FiltersTasks;

    public function __construct(private readonly TaskWorkflow $workflow)
    {
    }

    public function index(Request $request, TaskStatistics $stats): View
    {
        $client = $request->user();

        $query = Task::query()
            ->visibleTo($client)
            ->with(['agent:id,name,job_title', 'project:id,name,code,color']);

        $tasks = $this->applyTaskFilters($query, $request)
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('client.tasks.partials.grid', compact('tasks'));
        }

        return view('client.tasks.index', [
            'tasks' => $tasks,
            'filters' => $this->taskFilterState($request),
            'counts' => $stats->countsByStatus(Task::query()->where('client_id', $client->id)),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Task::class);

        return view('client.tasks.create', [
            'task' => new Task(['priority' => TaskPriority::Normal]),
            'projects' => Project::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = new Task($request->validated());
        $task->code = Task::generateCode();
        $task->client_id = $request->user()->id;
        $task->created_by = $request->user()->id;
        $task->status = TaskStatus::Pending;
        $task->save();

        $this->workflow->recordCreation($task, $request->user());

        return redirect()
            ->route('client.tasks.show', $task)
            ->with('success', 'Votre demande '.$task->code.' a ete transmise.');
    }

    public function show(Request $request, Task $task): View
    {
        Gate::authorize('view', $task);

        $task->load([
            'agent:id,name,email,job_title,role',
            'project:id,name,code,color',
            'statusHistories.user:id,name,role',
        ]);

        // Les notes internes ne sortent jamais vers le client.
        $comments = $task->comments()->where('is_internal', false)->with('user:id,name,role')->get();

        return view('client.tasks.show', [
            'task' => $task,
            'comments' => $comments,
            'transitions' => $this->workflow->availableTransitions($task, $request->user()),
        ]);
    }

    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);

        return view('client.tasks.edit', [
            'task' => $task,
            'projects' => Project::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route('client.tasks.show', $task)
            ->with('success', 'Votre demande '.$task->code.' a ete mise a jour.');
    }
}
