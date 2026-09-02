<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\FiltersTasks;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskStatistics;
use App\Services\TaskWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    use FiltersTasks;

    public function __construct(private readonly TaskWorkflow $workflow)
    {
    }

    public function index(Request $request, TaskStatistics $stats): View
    {
        $query = Task::query()
            ->with(['client:id,name,role', 'agent:id,name,role', 'project:id,name,code,color']);

        // Par defaut on masque les archives : elles ont leur propre ecran.
        if (! $request->filled('status') && ! $request->boolean('with_archived')) {
            $query->notArchived();
        }

        $tasks = $this->applyTaskFilters($query, $request)
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.tasks.partials.grid', compact('tasks'));
        }

        return view('admin.tasks.index', [
            'tasks' => $tasks,
            'filters' => $this->taskFilterState($request),
            'counts' => $stats->countsByStatus(),
            'agents' => $this->agents(),
            'clients' => $this->clients(),
            'projects' => $this->projects(),
        ]);
    }

    /** Vue Kanban : les six colonnes du cycle de vie. */
    public function board(Request $request): View
    {
        $query = Task::query()
            ->with(['client:id,name,role', 'agent:id,name,role', 'project:id,name,code,color']);

        $tasks = $this->applyTaskFilters($query, $request)
            ->limit(400)
            ->get()
            ->groupBy(fn (Task $task) => $task->status->value);

        $columns = [];
        foreach (TaskStatus::kanbanOrder() as $status) {
            $columns[$status->value] = [
                'status' => $status,
                'tasks' => $tasks->get($status->value, collect()),
            ];
        }

        return view('admin.tasks.board', [
            'columns' => $columns,
            'filters' => $this->taskFilterState($request),
            'agents' => $this->agents(),
            'clients' => $this->clients(),
            'projects' => $this->projects(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Task::class);

        return view('admin.tasks.create', [
            'task' => new Task(['priority' => TaskPriority::Normal, 'status' => TaskStatus::Pending]),
            'agents' => $this->agents(),
            'clients' => $this->clients(),
            'projects' => $this->projects(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $task = new Task($data);
        $task->code = Task::generateCode();
        $task->created_by = $request->user()->id;
        $task->status = filled($data['assigned_to'] ?? null) ? TaskStatus::Todo : TaskStatus::Pending;
        $task->save();

        $this->workflow->recordCreation($task, $request->user());

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'La tache '.$task->code.' a ete creee.');
    }

    public function show(Task $task): View
    {
        Gate::authorize('view', $task);

        $task->load([
            'client:id,name,email,company,role',
            'agent:id,name,email,job_title,role',
            'creator:id,name,role',
            'project:id,name,code,color',
            'statusHistories.user:id,name,role',
            'comments.user:id,name,role',
        ]);

        return view('admin.tasks.show', [
            'task' => $task,
            'transitions' => $this->workflow->availableTransitions($task, request()->user()),
            'agents' => $this->agents(),
        ]);
    }

    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);

        return view('admin.tasks.edit', [
            'task' => $task,
            'agents' => $this->agents(),
            'clients' => $this->clients(),
            'projects' => $this->projects(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'La tache '.$task->code.' a ete mise a jour.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $code = $task->code;
        $task->delete();

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'La tache '.$code.' a ete supprimee.');
    }

    /* -----------------------------------------------------------------
     | Listes de reference
     | ----------------------------------------------------------------- */

    private function agents()
    {
        return User::query()->agents()->active()->orderBy('name')->get(['id', 'name', 'job_title']);
    }

    private function clients()
    {
        return User::query()->clients()->active()->orderBy('name')->get(['id', 'name', 'company']);
    }

    private function projects()
    {
        return Project::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'color']);
    }
}
