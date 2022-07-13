<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\EntityDeleted;

class LeadDeleted extends EntityDeleted
{
    protected function getEntity(): string
    {
        return Lead::class;
    }
}
