<?php

namespace App\Domain\Users\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserPasswordUpdated extends ShouldBeStored
{
    public $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }
}
