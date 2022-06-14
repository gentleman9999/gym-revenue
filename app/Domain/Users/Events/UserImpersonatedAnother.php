<?php

namespace App\Domain\Users\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserImpersonatedAnother extends ShouldBeStored
{
    public string $invader;
    public string $victim;

    public function __construct(string $invader, string $victim)
    {
        $this->invader = $invader;
        $this->victim = $victim;
    }
}
