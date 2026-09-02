<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filtres partages par les listes de taches des trois espaces.
 *
 * Les valeurs recues sont systematiquement validees contre les enums metier :
 * une valeur inconnue est ignoree plutot que transmise a SQL.
 */
trait FiltersTasks
{
    protected function applyTaskFilters(Builder $query, Request $request): Builder
    {
        $query->search($request->query('q'));

        if ($status = TaskStatus::tryFrom((string) $request->query('status'))) {
            $query->where('status', $status->value);
        }

        if ($priority = TaskPriority::tryFrom((string) $request->query('priority'))) {
            $query->where('priority', $priority->value);
        }

        if ($request->filled('agent')) {
            $agent = $request->query('agent');
            $agent === 'none'
                ? $query->whereNull('assigned_to')
                : $query->where('assigned_to', (int) $agent);
        }

        if ($request->filled('client')) {
            $query->where('client_id', (int) $request->query('client'));
        }

        if ($request->filled('project')) {
            $query->where('project_id', (int) $request->query('project'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        if ($request->boolean('overdue')) {
            $query->whereNotNull('due_date')
                ->whereDate('due_date', '<', now())
                ->whereIn('status', TaskStatus::activeStatuses());
        }

        return $this->applyTaskSort($query, $request);
    }

    protected function applyTaskSort(Builder $query, Request $request): Builder
    {
        return match ($request->query('sort')) {
            'oldest' => $query->orderBy('created_at'),
            'priority' => $query->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END")->orderByDesc('created_at'),
            'due' => $query->orderByRaw('due_date IS NULL, due_date asc'),
            'title' => $query->orderBy('title'),
            default => $query->latest('created_at'),
        };
    }

    /** Parametres de filtre a re-injecter dans la vue et la pagination. */
    protected function taskFilterState(Request $request): array
    {
        return [
            'q' => $request->query('q', ''),
            'status' => $request->query('status', ''),
            'priority' => $request->query('priority', ''),
            'agent' => $request->query('agent', ''),
            'client' => $request->query('client', ''),
            'project' => $request->query('project', ''),
            'from' => $request->query('from', ''),
            'to' => $request->query('to', ''),
            'sort' => $request->query('sort', 'latest'),
            'overdue' => $request->boolean('overdue'),
        ];
    }
}
