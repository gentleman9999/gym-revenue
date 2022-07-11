<?php

namespace App\Domain\LeadStatuses\Events;

use App\Domain\LeadStatuses\LeadStatus;
use App\StorableEvents\EntityCreated;

class LeadStatusCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return LeadStatus::class;
    }
}
