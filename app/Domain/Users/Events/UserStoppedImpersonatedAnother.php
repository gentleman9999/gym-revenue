<?php

namespace App\Domain\Users\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserStoppedImpersonatedAnother extends ShouldBeStored
{
    /** @var string */
    public string $invader;
    /** @var string */
    public string $victim;

    public function __construct(string $invader, string $victim)
    {
        $this->invader = $invader;
        $this->victim = $victim;
    }
}
