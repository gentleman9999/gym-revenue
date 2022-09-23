<?php

namespace App\Domain\Campaigns\Events;

use App\Domain\Campaigns\CallOutcome;
use App\StorableEvents\EntityUpdated;

class CallOutcomeUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return CallOutcome::class;
    }
}
