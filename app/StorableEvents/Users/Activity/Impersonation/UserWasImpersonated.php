<?php

namespace App\StorableEvents\Users\Activity\Impersonation;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserWasImpersonated extends ShouldBeStored
{
    public $victim, $invader, $date;
    public function __construct($victim, $invader, $date)
    {
        $this->victim = $victim;
        $this->invader = $invader;
        $this->date = $date;
    }
}
