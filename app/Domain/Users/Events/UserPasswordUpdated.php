<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserPasswordUpdated extends GymRevShouldBeStored
{
    public $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }
}
