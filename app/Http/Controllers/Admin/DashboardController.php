<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskStatusHistory;
use App\Services\TaskStatistics;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(TaskStatistics $stats): View
    {
        $projects = Project::query()
            ->where('is_active', true)
            ->with('owner:id,name')
            ->withCount([
                'tasks',
                'members',
            ])
            ->orderByDesc('tasks_count')
            ->limit(4)
            ->get();

        $recentActivity = TaskStatusHistory::query()
            ->with(['task:id,code,title', 'user:id,name'])
            ->latest('id')
            ->limit(8)
            ->get();

        $recentComments = TaskComment::query()
            ->with(['task:id,code,title', 'user:id,name'])
            ->latest('id')
            ->limit(6)
            ->get();

        $urgentTasks = Task::query()
            ->urgent()
            ->whereIn('status', TaskStatus::activeStatuses())
            ->with(['client:id,name', 'agent:id,name'])
            ->orderByRaw('due_date IS NULL, due_date asc')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'overview' => $stats->adminOverview(),
            'workload' => $stats->workloadByAgent(),
            'projects' => $projects,
            'recentActivity' => $recentActivity,
            'recentComments' => $recentComments,
            'urgentTasks' => $urgentTasks,
        ]);
    }
}
