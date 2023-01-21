<?php

declare(strict_types=1);

namespace App\Domain\EngagementEvents\Projections;

use App\Domain\Users\Events\UserObfuscated;

class EngagementEventsProjector
{
    public function onUserObfuscated(UserObfuscated $event): void
    {
    }
}
