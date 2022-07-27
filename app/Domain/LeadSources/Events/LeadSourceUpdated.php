<?php

namespace App\Domain\LeadSources\Events;

use App\Domain\LeadSources\LeadSource;
use App\StorableEvents\EntityUpdated;

class LeadSourceUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return LeadSource::class;
    }
}
