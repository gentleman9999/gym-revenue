<?php

namespace App\Domain\LeadStatuses\Events;

use App\Domain\LeadStatuses\LeadStatus;
use App\StorableEvents\EntityUpdated;

class LeadStatusUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return LeadStatus::class;
    }
}