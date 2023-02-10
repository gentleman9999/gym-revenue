<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Projections;

use App\Domain\Locations\Projections\Location;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Database\Factories\LocationVendorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * LocationVendor
 *
 * @mixin Builder
 */
class LocationVendor extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    /** @var array<string>  */
    protected $fillable = [
        'name', 'vendor_category_id', 'location_id', 'poc_name', 'poc_email', 'poc_phone',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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
        return LocationVendorFactory::new();
    }
}
