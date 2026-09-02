<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Point d'entree unique du cycle de vie d'une tache.
 *
 * Toute modification de statut passe par ici : les regles de transition, les
 * horodatages metier et l'historique restent ainsi coherents, quel que soit
 * l'appelant (formulaire classique, endpoint AJAX, drag & drop du Kanban).
 */
class TaskWorkflow
{
    /**
     * Applique un changement de statut apres verification des regles metier.
     *
     * @throws RuntimeException si la transition n'est pas autorisee pour ce role
     */
    public function changeStatus(Task $task, TaskStatus $target, User $actor, ?string $comment = null): Task
    {
        $current = $task->status;

        if ($current === $target) {
            return $task;
        }

        if (! $current->canTransitionTo($target, $actor->role)) {
            throw new RuntimeException(sprintf(
                'Transition non autorisee : "%s" vers "%s".',
                $current->label(),
                $target->label()
            ));
        }

        return DB::transaction(function () use ($task, $current, $target, $actor, $comment) {
            $task->status = $target;
            $this->applyTimestamps($task, $target);
            $task->save();

            TaskStatusHistory::create([
                'task_id' => $task->id,
                'user_id' => $actor->id,
                'old_status' => $current->value,
                'new_status' => $target->value,
                'comment' => $comment,
            ]);

            return $task->refresh();
        });
    }

    /**
     * Assigne (ou desassigne) une tache a un agent.
     *
     * Une tache "En attente" qui recoit un agent bascule automatiquement en
     * "A faire" : c'est le moment ou la demande entre dans le flux de travail.
     */
    public function assign(Task $task, ?User $agent, User $actor): Task
    {
        return DB::transaction(function () use ($task, $agent, $actor) {
            $previousStatus = $task->status;

            $task->assigned_to = $agent?->id;

            if ($agent !== null && $previousStatus === TaskStatus::Pending) {
                $task->status = TaskStatus::Todo;
                $this->applyTimestamps($task, TaskStatus::Todo);
            }

            $task->save();

            TaskStatusHistory::create([
                'task_id' => $task->id,
                'user_id' => $actor->id,
                'old_status' => $previousStatus->value,
                'new_status' => $task->status->value,
                'comment' => $agent !== null
                    ? 'Tache assignee a '.$agent->name
                    : 'Assignation retiree',
            ]);

            return $task->refresh();
        });
    }

    /** Enregistre la creation d'une tache dans l'historique. */
    public function recordCreation(Task $task, User $actor): void
    {
        TaskStatusHistory::create([
            'task_id' => $task->id,
            'user_id' => $actor->id,
            'old_status' => null,
            'new_status' => $task->status->value,
            'comment' => 'Creation de la tache',
        ]);
    }

    /**
     * Options de statut proposables a l'utilisateur pour cette tache.
     *
     * @return array<int,array{value:string,label:string}>
     */
    public function availableTransitions(Task $task, User $actor): array
    {
        return array_map(
            fn (TaskStatus $status) => ['value' => $status->value, 'label' => $status->label()],
            $task->status->allowedTransitions($actor->role)
        );
    }

    /** Positionne les horodatages metier lies au nouveau statut. */
    protected function applyTimestamps(Task $task, TaskStatus $target): void
    {
        match ($target) {
            TaskStatus::InProgress => $task->started_at ??= now(),
            TaskStatus::Completed => $task->completed_at = now(),
            TaskStatus::Cancelled => $task->cancelled_at = now(),
            TaskStatus::Archived => $task->archived_at = now(),
            default => null,
        };

        // Retour en arriere : on nettoie les horodatages devenus faux.
        if (! in_array($target, [TaskStatus::Completed, TaskStatus::Archived], true)) {
            $task->completed_at = null;
        }

        if ($target !== TaskStatus::Cancelled) {
            $task->cancelled_at = null;
        }

        if ($target !== TaskStatus::Archived) {
            $task->archived_at = null;
        }
    }
}
