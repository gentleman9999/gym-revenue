<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\EntityUpdated;

class LeadUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return Lead::class;
    }
}
