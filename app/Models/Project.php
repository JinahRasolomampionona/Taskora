<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'owner_id',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role_in_project')
            ->withTimestamps();
    }

    public function getInitialsAttribute(): string
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim($this->name)) ?: []));

        if ($parts === []) {
            return '?';
        }

        $first = Str::upper(Str::substr($parts[0], 0, 1));
        $last = count($parts) > 1 ? Str::upper(Str::substr($parts[1], 0, 1)) : '';

        return $first.$last;
    }
}
