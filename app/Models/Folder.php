<?php

namespace App\Models;

use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;
    use Sortable;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'team_ids', 'location_ids', 'user_ids', 'position_ids', 'department_ids', 'role_ids']; //'deleted_at'

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
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

    public function hasUserIds()
    {
        return $this->whereNotNull('user_ids')->exists();
    }

    public function hasLocationIds()
    {
        return $this->whereNotNull('location_ids')->exists();
    }

    public function hasTeamIds()
    {
        return $this->whereNotNull('team_ids')->exists();
    }

    public function hasPositionIds()
    {
        return $this->whereNotNull('position_ids')->exists();
    }

    public function hasDepartmentIds()
    {
        return $this->whereNotNull('department_ids')->exists();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
