<?php

namespace App\Http\Controllers\Client;

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
        $client = $request->user();

        $tasks = Task::query()
            ->where('client_id', $client->id)
            ->whereIn('status', TaskStatus::activeStatuses())
            ->with(['agent:id,name,job_title', 'project:id,name,code,color'])
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END")
            ->latest('created_at')
            ->limit(6)
            ->get();

        $recent = Task::query()
            ->where('client_id', $client->id)
            ->where('status', TaskStatus::Completed->value)
            ->with('agent:id,name')
            ->latest('completed_at')
            ->limit(5)
            ->get();

        return view('client.dashboard', [
            'overview' => $stats->clientOverview($client),
            'tasks' => $tasks,
            'recent' => $recent,
        ]);
    }
}
