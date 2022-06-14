<?php

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserWelcomeEmailSent extends GymRevShouldBeStored
{
    public $user;

    public function __construct(string $user)
    {
        $this->user = $user;
    }
}
