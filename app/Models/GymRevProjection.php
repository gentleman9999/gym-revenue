<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Collection;
use Spatie\EventSourcing\Projections\Projection;

/**
 * Another shared base class that we use instead of
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

    public function addOrUpdateDetails(string $field, ?string $value, $misc = null, $active = 1): void
    {
        $detail = [
            'field' => $field,
            'value' => $value,
            'misc' => $misc,
            'active' => $active,
        ];

        $details = $this->details ?: new Collection();

        if ($details->where('field', $field)->count() > 0) {
            $details = $details->transform(
                function ($item, $key) use ($field, $detail) {
                    if ($item['field'] === $field) {
                        return $detail;
                    }

                    return $item;
                }
            );
        } else {
            $details->push($detail);
        }

        $this->details = $details;
    }
}
