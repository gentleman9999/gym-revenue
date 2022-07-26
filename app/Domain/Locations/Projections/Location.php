<?php

namespace App\Domain\Locations\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Location
 *
 * @mixin Builder
 */
class Location extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    protected $fillable = [
        'name', 'address1', 'address2', 'city', 'state', 'zip',
        'active', 'location_no', 'gymrevenue_id', 'deleted_at',
        'open_date', 'close_date', 'phone', 'default_team_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(LocationDetails::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(LocationDetails::class);
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('city', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['state'] ?? null, function ($query, $state) {
            $query->where('state', 'like', '%'.$state.'%');
        });
    }

    public function defaultTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'default_team_id');
    }
}
