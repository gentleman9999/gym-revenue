<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasFactory;
    use Sortable;
    use SoftDeletes;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $fillable = [
        'id',
        'name',
        'team_ids',
        'location_ids',
        'user_ids',
        'position_ids',
        'department_ids',
        'role_ids',
    ]; //'deleted_at'

    /** @var array<string, string> */
    protected $casts = [
        'team_ids' => 'array',
        'location_ids' => 'array',
        'user_ids' => 'array',
        'position_ids' => 'array',
        'department_ids' => 'array',
        'role_ids' => 'array',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'folder', 'id');
    }

    public function hasUserIds(): bool
    {
        return $this->whereNotNull('user_ids')->exists();
    }

    public function hasLocationIds(): bool
    {
        return $this->whereNotNull('location_ids')->exists();
    }

    public function hasTeamIds(): bool
    {
        return $this->whereNotNull('team_ids')->exists();
    }

    public function hasPositionIds(): bool
    {
        return $this->whereNotNull('position_ids')->exists();
    }

    public function hasDepartmentIds(): bool
    {
        return $this->whereNotNull('department_ids')->exists();
    }

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed): void {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
