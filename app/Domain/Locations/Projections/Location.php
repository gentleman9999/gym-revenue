<?php

namespace App\Domain\Locations\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use App\Enums\LocationTypeEnum;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Location
 *
 * @mixin Builder
 * @method static \Database\Factories\LocationFactory factory()
 */
class Location extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    protected $fillable = [
        'name', 'address1', 'address2', 'city', 'state', 'zip',
        'active', 'location_no', 'gymrevenue_id',
        'opened_at', 'closed_at', 'phone', 'default_team_id',
        'location_type', 'latitude', 'longitude',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    protected $casts = [
        'location_type' => LocationTypeEnum::class,
    ];

    protected const DELETED_AT = 'closed_at';

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return LocationFactory::new();
    }

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
        })->when($filters['closed'] ?? null, function ($query, $closed) {
            if ($closed === 'with') {
                $query->withTrashed();
            } elseif ($closed === 'only') {
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
