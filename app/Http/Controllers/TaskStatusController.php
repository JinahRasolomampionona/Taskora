<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Services\TaskWorkflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

/**
 * Changement de statut d'une tache, partage par les trois espaces.
 *
 * Repond en JSON pour le Kanban et les actions rapides, en redirection pour
 * les formulaires classiques (progressive enhancement).
 */
class TaskStatusController extends Controller
{
    public function __construct(private readonly TaskWorkflow $workflow)
    {
    }

    public function update(UpdateTaskStatusRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        try {
            $task = $this->workflow->changeStatus(
                $task,
                $request->status(),
                $request->user(),
                $request->validated('comment')
            );
        } catch (RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }

        $message = 'Statut mis a jour : '.$task->status->label().'.';

        if ($request->expectsJson()) {
            $task->loadMissing(['agent:id,name', 'client:id,name']);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $task->id,
                    'code' => $task->code,
                    'status' => $task->status->value,
                    'status_label' => $task->status->label(),
                    'status_class' => $task->status->badgeClass(),
                    'progress' => $task->progress,
                    'transitions' => $this->workflow->availableTransitions($task, $request->user()),
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
