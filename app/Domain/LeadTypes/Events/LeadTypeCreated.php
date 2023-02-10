<?php

declare(strict_types=1);

namespace App\Domain\LeadTypes\Events;

use App\Domain\LeadTypes\LeadType;
use App\StorableEvents\EntityCreated;

class LeadTypeCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return LeadType::class;
    }
}
