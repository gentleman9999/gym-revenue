<?php

namespace App\StorableEvents\Users\Activity\API;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AccessTokenGranted extends ShouldBeStored
{
    public $user;

    public function __construct(string $user)
    {
        $this->user = $user;
    }
}
