<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'job_title',
        'phone',
        'company',
        'avatar_path',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => UserRole::class,
        ];
    }

    /* -----------------------------------------------------------------
     | Relations
     | ----------------------------------------------------------------- */

    /** Demandes créées par ce client. */
    public function tasksAsClient(): HasMany
    {
        return $this->hasMany(Task::class, 'client_id');
    }

    /** Tâches assignées à cet agent. */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /** Tâches saisies par cet utilisateur (admin ou client). */
    public function tasksCreated(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function taskStatusHistories(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class);
    }

    public function taskComments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('role_in_project')
            ->withTimestamps();
    }

    /* -----------------------------------------------------------------
     | Rôles
     | ----------------------------------------------------------------- */

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isAgent(): bool
    {
        return $this->role === UserRole::Agent;
    }

    public function isClient(): bool
    {
        return $this->role === UserRole::Client;
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        foreach ($roles as $role) {
            $value = $role instanceof UserRole ? $role : UserRole::tryFrom($role);
            if ($value !== null && $this->role === $value) {
                return true;
            }
        }

        return false;
    }

    public function homeRoute(): string
    {
        return $this->role->homeRoute();
    }

    /* -----------------------------------------------------------------
     | Présentation
     | ----------------------------------------------------------------- */

    /** Initiales utilisées par les avatars (2 lettres max). */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $parts = array_values(array_filter($parts));

        if ($parts === []) {
            return '?';
        }

        $first = Str::upper(Str::substr($parts[0], 0, 1));
        $last = count($parts) > 1 ? Str::upper(Str::substr(end($parts), 0, 1)) : '';

        return $first.$last;
    }

    /** Couleur d'avatar déterministe, dérivée de l'identifiant. */
    public function getAvatarColorAttribute(): string
    {
        $palette = ['#2563eb', '#0891b2', '#7c3aed', '#c2410c', '#059669', '#db2777', '#4f46e5', '#b45309'];

        return $palette[($this->id ?? 0) % count($palette)];
    }

    /* -----------------------------------------------------------------
     | Scopes
     | ----------------------------------------------------------------- */

    public function scopeRole($query, UserRole $role)
    {
        return $query->where('role', $role->value);
    }

    public function scopeAgents($query)
    {
        return $query->where('role', UserRole::Agent->value);
    }

    public function scopeClients($query)
    {
        return $query->where('role', UserRole::Client->value);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
