<?php

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserWasImpersonated extends GymRevShouldBeStored
{
    public $victim;
    public $invader;

    public function __construct(string $victim, string $invader)
    {
        $this->victim = $victim;
        $this->invader = $invader;
    }
}
