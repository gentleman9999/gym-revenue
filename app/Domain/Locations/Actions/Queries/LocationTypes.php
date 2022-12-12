<?php

declare(strict_types=1);

namespace App\Domain\Locations\Actions\Queries;

use App\Actions\GymRevAction;
use App\Domain\Locations\Enums\LocationType;

class LocationTypes extends GymRevAction
{
    public function handle(): array
    {
        return LocationType::asOptionsArray();
    }
}
