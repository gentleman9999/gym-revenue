<?php

namespace App\Domain\LocationEmployees\Projections;

use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Location
 *
 * @mixin Builder
 * @method static \Database\Factories\LocationFactory factory()
 */
class LocationEmployee extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    protected $fillable = [
        'client_id', 'location_id', 'department_id', 'position_id', 'user_id', 'primary_supervisor_user_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return LocationFactory::new();
    }
}
