<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Normal = 'normal';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::Urgent => 'Urgent',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Normal => 'tk-badge-priority-normal',
            self::Urgent => 'tk-badge-priority-urgent',
        };
    }

    /** @return array<string,string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $p) => [$p->value => $p->label()])
            ->all();
    }
}
