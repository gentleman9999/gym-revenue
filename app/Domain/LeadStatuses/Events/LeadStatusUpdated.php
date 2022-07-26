<?php

namespace App\Domain\LeadStatuses\Events;

use App\Domain\LeadStatuses\LeadStatus;
use App\StorableEvents\EntityUpdated;

class LeadStatusUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return LeadStatus::class;
    }
}
