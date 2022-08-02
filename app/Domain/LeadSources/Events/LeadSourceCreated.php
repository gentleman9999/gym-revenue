<?php

namespace App\Domain\LeadSources\Events;

use App\Domain\LeadSources\LeadSource;
use App\StorableEvents\EntityCreated;

class LeadSourceCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return LeadSource::class;
    }
}
