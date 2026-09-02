<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskWorkflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TaskAssignmentController extends Controller
{
    public function __construct(private readonly TaskWorkflow $workflow)
    {
    }

    public function update(AssignTaskRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        $agentId = $request->validated('assigned_to');
        $agent = $agentId ? User::find($agentId) : null;

        $task = $this->workflow->assign($task, $agent, $request->user());

        $message = $agent
            ? 'Tache assignee a '.$agent->name.'.'
            : "L'assignation a ete retiree.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $task->id,
                    'status' => $task->status->value,
                    'status_label' => $task->status->label(),
                    'status_class' => $task->status->badgeClass(),
                    'agent' => $agent ? [
                        'id' => $agent->id,
                        'name' => $agent->name,
                        'initials' => $agent->initials,
                        'color' => $agent->avatar_color,
                    ] : null,
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
