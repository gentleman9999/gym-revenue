<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\EntityCreated;

class LeadCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return Lead::class;
    }
}
