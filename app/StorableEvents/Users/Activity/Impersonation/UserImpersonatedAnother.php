<?php

namespace App\StorableEvents\Users\Activity\Impersonation;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserImpersonatedAnother extends ShouldBeStored
{
    public $invader, $victim, $date;
    public function __construct($invader, $victim, $date)
    {
        $this->invader = $invader;
        $this->victim = $victim;
        $this->date = $date;
    }
}
