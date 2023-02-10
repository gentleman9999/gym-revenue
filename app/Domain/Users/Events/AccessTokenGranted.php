<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class AccessTokenGranted extends GymRevShouldBeStored
{
}
