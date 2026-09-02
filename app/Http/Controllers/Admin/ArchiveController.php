<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\FiltersTasks;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Consultation des taches sorties du flux actif : archives et annulations.
 */
class ArchiveController extends Controller
{
    use FiltersTasks;

    public function index(Request $request): View
    {
        $scope = $request->query('scope') === 'cancelled'
            ? TaskStatus::Cancelled
            : TaskStatus::Archived;

        $query = Task::query()
            ->where('status', $scope->value)
            ->with(['client:id,name', 'agent:id,name', 'project:id,name,code,color']);

        $tasks = $this->applyTaskSort($query->search($request->query('q')), $request)
            ->paginate(12)
            ->withQueryString();

        return view('admin.archives.index', [
            'tasks' => $tasks,
            'scope' => $scope,
            'filters' => $this->taskFilterState($request),
            'agents' => User::query()->agents()->orderBy('name')->get(['id', 'name']),
            'archivedCount' => Task::query()->archived()->count(),
            'cancelledCount' => Task::query()->status(TaskStatus::Cancelled)->count(),
        ]);
    }
}
