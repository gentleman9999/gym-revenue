<?php

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserStoppedImpersonatedAnother extends GymRevShouldBeStored
{
    /** @var string */
    public string $invader;
    /** @var string */
    public string $victim;

    public function __construct(string $invader, string $victim)
    {
        $this->invader = $invader;
        $this->victim = $victim;
    }
}
