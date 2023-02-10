<?php

declare(strict_types=1);

namespace App\Domain\Locations\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Enums\LocationType;
use App\Domain\Teams\Models\Team;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected const DELETED_AT = 'closed_at';

    /** @var array<string> */
    protected $fillable = [
        'name',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'active',
        'location_no',
        'gymrevenue_id',
        'opened_at',
        'closed_at',
        'phone',
        'default_team_id',
        'location_type',
        'latitude',
        'longitude',
        'capacity',
        'presale_started_at',
        'presale_opened_at',
        'details',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'location_type' => LocationType::class,
        'details' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function poc_phone_detail(): string|null
    {
        return $this->detail['poc_phone'] ?? null;
    }

    public function pocFirstDetail(): string|null
    {
        return $this->detail['poc_first'] ?? null;
    }

    public function pocLastDetail(): string|null
    {
        return $this->detail['poc_last'] ?? null;
    }

    public function getPocLastAttribute(): string|null
    {
        return $this->pocLastDetail() ?? null;
    }

    public function getPocFirstAttribute(): string|null
    {
        return $this->pocFirstDetail() ?? null;
    }

    public function getPocPhoneAttribute(): string|null
    {
        return $this->pocPhoneDetail->value ?? null;
    }

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('city', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function ($query) use ($search): void {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        })->when($filters['closed'] ?? null, function ($query, $closed): void {
            if ($closed === 'with') {
                $query->withTrashed();
            } elseif ($closed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['state'] ?? null, function ($query, $state): void {
            $query->where('state', 'like', '%' . $state . '%');
        });
    }

    public function defaultTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'default_team_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    /**
     * Create a new factory instance for the model.
     *
     */
    protected static function newFactory(): Factory
    {
        return LocationFactory::new();
    }
}
