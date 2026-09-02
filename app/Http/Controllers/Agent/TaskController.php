<?php

namespace App\Http\Controllers\Agent;

use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\FiltersTasks;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskStatistics;
use App\Services\TaskWorkflow;
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
        $agent = $request->user();

        $query = Task::query()
            ->visibleTo($agent)
            ->with(['client:id,name,company', 'project:id,name,code,color']);

        $tasks = $this->applyTaskFilters($query, $request)
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('agent.tasks.partials.grid', compact('tasks'));
        }

        return view('agent.tasks.index', [
            'tasks' => $tasks,
            'filters' => $this->taskFilterState($request),
            'counts' => $stats->countsByStatus(Task::query()->where('assigned_to', $agent->id)),
        ]);
    }

    /** Vue Kanban limitee au perimetre de l'agent. */
    public function board(Request $request): View
    {
        $agent = $request->user();

        $grouped = $this->applyTaskFilters(
            Task::query()->visibleTo($agent)->with(['client:id,name', 'project:id,name,code,color']),
            $request
        )->limit(300)->get()->groupBy(fn (Task $task) => $task->status->value);

        $columns = [];
        foreach (TaskStatus::kanbanOrder() as $status) {
            $columns[$status->value] = [
                'status' => $status,
                'tasks' => $grouped->get($status->value, collect()),
            ];
        }

        return view('agent.tasks.board', [
            'columns' => $columns,
            'filters' => $this->taskFilterState($request),
        ]);
    }

    public function show(Request $request, Task $task): View
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

        return view('agent.tasks.show', [
            'task' => $task,
            'transitions' => $this->workflow->availableTransitions($task, $request->user()),
        ]);
    }

    /** Historique : taches terminees, annulees ou archivees de l'agent. */
    public function history(Request $request): View
    {
        $agent = $request->user();

        $tasks = Task::query()
            ->visibleTo($agent)
            ->whereIn('status', [
                TaskStatus::Completed->value,
                TaskStatus::Cancelled->value,
                TaskStatus::Archived->value,
            ])
            ->with(['client:id,name', 'project:id,name,code,color'])
            ->search($request->query('q'))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('agent.tasks.history', [
            'tasks' => $tasks,
            'filters' => $this->taskFilterState($request),
        ]);
    }
}
