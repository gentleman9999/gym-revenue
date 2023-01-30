<?php

declare(strict_types=1);

namespace App\Domain\Locations\Enums;

use App\Enums\GymRevEnum;
use BenSampo\Enum\Attributes\Description;

#[Description('Available Location Types')]
class LocationType extends GymRevEnum
{
    #[Description('Locations that are available to the public')]
    public const STORE = 'store';

    #[Description('Locations that are not generally available to the public')]
    public const OFFICE = 'office';

    #[Description('Locations that are considered corporate headquarter offices')]
    public const HQ = 'headquarters';
}
