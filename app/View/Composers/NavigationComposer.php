<?php

namespace App\View\Composers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Compteurs affiches dans la sidebar et la cloche de notifications.
 *
 * Un seul groupBy par requete HTTP, memorise pour la duree du cycle.
 */
class NavigationComposer
{
    private ?array $cache = null;

    public function compose(View $view): void
    {
        $view->with('navCounts', $this->counts());
    }

    private function counts(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $user = auth()->user();

        if ($user === null) {
            return $this->cache = [];
        }

        $base = Task::query()->visibleTo($user);

        $byStatus = $base->clone()
            ->toBase()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        $counts = [];
        foreach (TaskStatus::cases() as $status) {
            $counts[$status->value] = (int) ($byStatus[$status->value] ?? 0);
        }

        $counts['active'] = $counts[TaskStatus::Pending->value]
            + $counts[TaskStatus::Todo->value]
            + $counts[TaskStatus::InProgress->value];

        $counts['total'] = array_sum(array_map(
            fn (TaskStatus $s) => $counts[$s->value],
            TaskStatus::cases()
        ));

        if ($user->isAdmin()) {
            $counts['unassigned'] = Task::query()
                ->whereNull('assigned_to')
                ->whereIn('status', TaskStatus::activeStatuses())
                ->count();
            $counts['agents'] = User::query()->agents()->count();
            $counts['clients'] = User::query()->clients()->count();
            $counts['alerts'] = $counts['unassigned'];
        } elseif ($user->isAgent()) {
            $counts['alerts'] = $counts[TaskStatus::Todo->value];
        } else {
            $counts['alerts'] = $counts[TaskStatus::Pending->value];
        }

        return $this->cache = $counts;
    }
}
