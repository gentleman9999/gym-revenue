<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserStoppedImpersonatedAnother extends GymRevShouldBeStored
{
    public string $invader;
    public string $victim;

    public function __construct(string $invader, string $victim)
    {
        $this->invader = $invader;
        $this->victim  = $victim;
    }
}
