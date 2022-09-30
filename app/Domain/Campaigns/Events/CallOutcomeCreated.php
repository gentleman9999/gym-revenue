<?php

namespace App\Domain\Campaigns\Events;

use App\Domain\Campaigns\CallOutcome;
use App\StorableEvents\EntityCreated;

class CallOutcomeCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return CallOutcome::class;
    }
}
