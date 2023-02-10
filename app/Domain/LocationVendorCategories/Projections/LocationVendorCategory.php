<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories\Projections;

use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * LocationVendorCategories
 *
 * @mixin Builder
 * @method static \Database\Factories\LocationVendorCategoriesFactory factory()
 */
class LocationVendorCategory extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    /** @var array<string>  */
    protected $fillable = [
        'name',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
