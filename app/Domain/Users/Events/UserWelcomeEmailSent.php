<?php

namespace App\Domain\Users\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserWelcomeEmailSent extends ShouldBeStored
{
    public $user;

    public function __construct(string $user)
    {
        $this->user = $user;
    }
}
