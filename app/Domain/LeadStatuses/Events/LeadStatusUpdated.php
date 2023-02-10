<?php

declare(strict_types=1);

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
