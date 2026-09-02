<?php

namespace App\Policies;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAgent()) {
            return $task->assigned_to === $user->id;
        }

        return $task->client_id === $user->id;
    }

    /** Admin et client peuvent creer une demande ; l'agent ne cree pas de tache. */
    public function create(User $user): bool
    {
        return $user->is_active && ($user->isAdmin() || $user->isClient());
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Le client ne peut retoucher sa demande que tant qu'elle n'est pas prise en charge.
        return $user->isClient()
            && $task->client_id === $user->id
            && $task->status === TaskStatus::Pending;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }

    /** Seul l'admin distribue le travail. */
    public function assign(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }

    /**
     * L'utilisateur peut-il agir sur le statut de cette tache ?
     *
     * Les transitions realisables sont ensuite filtrees par TaskStatus.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        if (! $this->view($user, $task)) {
            return false;
        }

        return $task->status->allowedTransitions($user->role) !== [];
    }

    public function archive(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }

    public function comment(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    /** Les notes internes restent invisibles pour le client. */
    public function viewInternalNotes(User $user, Task $task): bool
    {
        return $user->isAdmin() || ($user->isAgent() && $task->assigned_to === $user->id);
    }
}
