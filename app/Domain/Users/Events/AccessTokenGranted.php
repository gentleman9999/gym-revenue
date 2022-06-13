<?php

namespace App\Domain\Users\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AccessTokenGranted extends ShouldBeStored
{
    public function __construct()
    {
    }
}
