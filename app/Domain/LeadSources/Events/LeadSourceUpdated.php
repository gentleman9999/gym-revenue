<?php

namespace App\Domain\LeadSources\Events;

use App\Domain\LeadSources\LeadSource;
use App\StorableEvents\EntityUpdated;

class LeadSourceUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return LeadSource::class;
    }
}
