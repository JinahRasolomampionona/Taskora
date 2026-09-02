<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'project_id',
        'client_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'mission_details',
        'status',
        'priority',
        'estimated_minutes',
        'spent_minutes',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'archived_at' => 'datetime',
            'estimated_minutes' => 'integer',
            'spent_minutes' => 'integer',
        ];
    }

    /* -----------------------------------------------------------------
     | Relations
     | ----------------------------------------------------------------- */

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class)->latest('id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->latest('id');
    }

    /* -----------------------------------------------------------------
     | Scopes
     | ----------------------------------------------------------------- */

    public function scopeStatus(Builder $query, TaskStatus|string $status): Builder
    {
        return $query->where('status', $status instanceof TaskStatus ? $status->value : $status);
    }

    public function scopeNotArchived(Builder $query): Builder
    {
        return $query->where('status', '!=', TaskStatus::Archived->value);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::Archived->value);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', TaskPriority::Urgent->value);
    }

    /** Restreint la requete au perimetre visible par l'utilisateur donne. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match (true) {
            $user->isAdmin() => $query,
            $user->isAgent() => $query->where('assigned_to', $user->id),
            default => $query->where('client_id', $user->id),
        };
    }

    /** Recherche simple sur le code, le titre et la description. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';
            $q->where('code', 'like', $like)
                ->orWhere('title', 'like', $like)
                ->orWhere('description', 'like', $like);
        });
    }

    /* -----------------------------------------------------------------
     | Helpers metier / presentation
     | ----------------------------------------------------------------- */

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && ! in_array($this->status, [TaskStatus::Completed, TaskStatus::Cancelled, TaskStatus::Archived], true);
    }

    public function isUrgent(): bool
    {
        return $this->priority === TaskPriority::Urgent;
    }

    /** Estimation formatee : "45 min", "2 h", "1 j 4 h". */
    public function getEstimatedForHumansAttribute(): ?string
    {
        return self::minutesForHumans($this->estimated_minutes);
    }

    public function getSpentForHumansAttribute(): ?string
    {
        return self::minutesForHumans($this->spent_minutes);
    }

    public static function minutesForHumans(?int $minutes): ?string
    {
        if ($minutes === null || $minutes <= 0) {
            return null;
        }

        // Une journee de travail vaut 8 heures.
        $days = intdiv($minutes, 60 * 8);
        $hours = intdiv($minutes % (60 * 8), 60);
        $rest = $minutes % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days.' j';
        }
        if ($hours > 0) {
            $parts[] = $hours.' h';
        }
        if ($rest > 0) {
            $parts[] = $rest.' min';
        }

        return implode(' ', $parts);
    }

    /** Avancement indicatif (%) deduit du statut. */
    public function getProgressAttribute(): int
    {
        return match ($this->status) {
            TaskStatus::Pending => 5,
            TaskStatus::Todo => 25,
            TaskStatus::InProgress => 60,
            TaskStatus::Completed, TaskStatus::Archived => 100,
            TaskStatus::Cancelled => 0,
        };
    }

    /** Genere le prochain code sequentiel (TSK-0001). */
    public static function generateCode(): string
    {
        $last = (int) (static::query()->max('id') ?? 0);

        return sprintf('TSK-%04d', $last + 1);
    }
}
