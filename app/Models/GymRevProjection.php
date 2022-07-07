<?php

namespace App\Models;

use Spatie\EventSourcing\Projections\Projection;

/**
 * another shared base class that we use instead of
 * the vanilla Projection.  Since we use "id" as id,
 * and not uuid, using this class fixes that. It
 * also gives us a nice tap into all our projections
 * from a single place if the need arises.
 */
abstract class GymRevProjection extends Projection
{
    public function getKeyName()
    {
        return 'id';
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
