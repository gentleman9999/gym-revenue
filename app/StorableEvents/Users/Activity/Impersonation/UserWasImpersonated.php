<?php

namespace App\StorableEvents\Users\Activity\Impersonation;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserWasImpersonated extends ShouldBeStored
{
    public $victim;
    public $invader;

    public function __construct(string $victim, string $invader)
    {
        $this->victim = $victim;
        $this->invader = $invader;
    }
}
