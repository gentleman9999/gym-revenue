<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class WelcomeEmailSent extends ShouldBeStored
{
    public $user;

    public function __construct(string $user)
    {
        $this->user = $user;
    }
}
