<?php

namespace App\Domain\LeadTypes\Events;

use App\Domain\LeadTypes\LeadType;
use App\StorableEvents\EntityCreated;

class LeadTypeCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return LeadType::class;
    }
}
