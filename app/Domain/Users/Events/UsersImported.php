<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UsersImported extends GymRevShouldBeStored
{
    public $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }
}
