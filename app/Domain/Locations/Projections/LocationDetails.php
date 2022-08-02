<?php

namespace App\Domain\Locations\Projections;

use App\Domain\Locations\BelongsTo;
use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationDetails extends GymRevDetailProjection
{
    use SoftDeletes;

    public function location(): BelongsTo
    {
        return $this->parentModel;
    }

    public static function getRelatedModel()
    {
        return new Location();
    }

    public static function fk(): string
    {
        return "location_id";
    }
}
