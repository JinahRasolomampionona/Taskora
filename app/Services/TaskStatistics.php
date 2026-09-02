<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Agregations du tableau de bord.
 *
 * Tous les comptages sont realises en SQL : aucune collection de taches n'est
 * chargee en memoire uniquement pour etre comptee.
 */
class TaskStatistics
{
    /**
     * Repartition des taches par statut pour une requete donnee.
     *
     * @return array<string,int> statut technique => nombre de taches
     */
    public function countsByStatus(?Builder $query = null): array
    {
        $rows = ($query ?? Task::query())
            ->toBase()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        $counts = [];
        foreach (TaskStatus::cases() as $status) {
            $counts[$status->value] = (int) ($rows[$status->value] ?? 0);
        }

        $counts['total'] = array_sum($counts);
        $counts['active'] = $counts[TaskStatus::Pending->value]
            + $counts[TaskStatus::Todo->value]
            + $counts[TaskStatus::InProgress->value];

        return $counts;
    }

    /** Statistiques globales de l'espace administrateur. */
    public function adminOverview(): array
    {
        $counts = $this->countsByStatus();

        $userCounts = User::query()
            ->toBase()
            ->select('role', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('role')
            ->pluck('aggregate', 'role')
            ->all();

        return [
            'tasks' => $counts,
            'urgent' => Task::query()->urgent()->notArchived()->count(),
            'overdue' => Task::query()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now())
                ->whereIn('status', TaskStatus::activeStatuses())
                ->count(),
            'unassigned' => Task::query()
                ->whereNull('assigned_to')
                ->whereIn('status', TaskStatus::activeStatuses())
                ->count(),
            'agents' => (int) ($userCounts[UserRole::Agent->value] ?? 0),
            'clients' => (int) ($userCounts[UserRole::Client->value] ?? 0),
            'admins' => (int) ($userCounts[UserRole::Admin->value] ?? 0),
        ];
    }

    /** Statistiques du perimetre d'un agent. */
    public function agentOverview(User $agent): array
    {
        $counts = $this->countsByStatus(Task::query()->where('assigned_to', $agent->id));

        return [
            'tasks' => $counts,
            'urgent' => Task::query()
                ->where('assigned_to', $agent->id)
                ->urgent()
                ->whereIn('status', TaskStatus::activeStatuses())
                ->count(),
            'overdue' => Task::query()
                ->where('assigned_to', $agent->id)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now())
                ->whereIn('status', TaskStatus::activeStatuses())
                ->count(),
        ];
    }

    /** Statistiques du perimetre d'un client. */
    public function clientOverview(User $client): array
    {
        $counts = $this->countsByStatus(Task::query()->where('client_id', $client->id));

        return [
            'tasks' => $counts,
            'urgent' => Task::query()
                ->where('client_id', $client->id)
                ->urgent()
                ->whereIn('status', TaskStatus::activeStatuses())
                ->count(),
        ];
    }

    /**
     * Charge de travail par agent (taches actives et terminees).
     *
     * @return \Illuminate\Support\Collection<int,User>
     */
    public function workloadByAgent(int $limit = 6)
    {
        return User::query()
            ->agents()
            ->active()
            ->withCount([
                'assignedTasks as active_tasks_count' => fn ($q) => $q->whereIn('status', TaskStatus::activeStatuses()),
                'assignedTasks as completed_tasks_count' => fn ($q) => $q->where('status', TaskStatus::Completed->value),
                'assignedTasks as urgent_tasks_count' => fn ($q) => $q->urgent()->whereIn('status', TaskStatus::activeStatuses()),
            ])
            ->orderByDesc('active_tasks_count')
            ->limit($limit)
            ->get();
    }
}
