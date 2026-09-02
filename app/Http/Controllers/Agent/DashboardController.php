<?php

namespace App\Http\Controllers\Agent;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskStatistics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, TaskStatistics $stats): View
    {
        $agent = $request->user();

        $tasks = Task::query()
            ->where('assigned_to', $agent->id)
            ->whereIn('status', TaskStatus::activeStatuses())
            ->with(['client:id,name,company', 'project:id,name,code,color'])
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END")
            ->orderByRaw('due_date IS NULL, due_date asc')
            ->limit(9)
            ->get();

        $recentlyCompleted = Task::query()
            ->where('assigned_to', $agent->id)
            ->where('status', TaskStatus::Completed->value)
            ->with('client:id,name')
            ->latest('completed_at')
            ->limit(5)
            ->get();

        return view('agent.dashboard', [
            'overview' => $stats->agentOverview($agent),
            'tasks' => $tasks,
            'recentlyCompleted' => $recentlyCompleted,
        ]);
    }
}
