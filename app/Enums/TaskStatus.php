<?php

namespace App\Enums;

/**
 * Cycle de vie d'une tâche Taskora.
 *
 * Les valeurs stockées en base sont techniques et stables ; les libellés
 * français ne servent qu'à l'affichage et ne portent aucune logique métier.
 */
enum TaskStatus: string
{
    case Pending = 'pending';
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Todo => 'À faire',
            self::InProgress => 'En cours',
            self::Completed => 'Terminer',
            self::Cancelled => 'Annuler',
            self::Archived => 'Archive',
        };
    }

    public function badgeClass(): string
    {
        return 'tk-badge-status-'.str_replace('_', '-', $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Todo => 'list-check',
            self::InProgress => 'play-circle',
            self::Completed => 'check-circle',
            self::Cancelled => 'x-circle',
            self::Archived => 'archive',
        };
    }

    /** Statuts affichés sur le tableau Kanban, dans l'ordre. */
    public static function kanbanOrder(): array
    {
        return [
            self::Pending,
            self::Todo,
            self::InProgress,
            self::Completed,
            self::Cancelled,
            self::Archived,
        ];
    }

    /** Statuts considérés comme « actifs » (travail en cours). */
    public static function activeStatuses(): array
    {
        return [self::Pending->value, self::Todo->value, self::InProgress->value];
    }

    /**
     * Transitions métier autorisées, par rôle.
     *
     * C'est LA source de vérité du workflow : aucune vue ni contrôleur ne doit
     * redéfinir ces règles.
     *
     * @return array<int,self>
     */
    public function allowedTransitions(UserRole $role): array
    {
        return match ($role) {
            UserRole::Admin => match ($this) {
                self::Pending => [self::Todo, self::InProgress, self::Cancelled],
                self::Todo => [self::Pending, self::InProgress, self::Cancelled],
                self::InProgress => [self::Todo, self::Completed, self::Cancelled],
                self::Completed => [self::InProgress, self::Archived],
                self::Cancelled => [self::Pending, self::Archived],
                self::Archived => [self::Pending, self::Completed],
            },
            // L'agent fait avancer le travail qui lui est assigné.
            UserRole::Agent => match ($this) {
                self::Pending => [self::Todo],
                self::Todo => [self::InProgress],
                self::InProgress => [self::Todo, self::Completed],
                default => [],
            },
            // Le client ne peut qu'annuler une demande non encore prise en charge.
            UserRole::Client => match ($this) {
                self::Pending => [self::Cancelled],
                default => [],
            },
        };
    }

    public function canTransitionTo(self $target, UserRole $role): bool
    {
        return in_array($target, $this->allowedTransitions($role), true);
    }

    /** @return array<string,string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->all();
    }
}
