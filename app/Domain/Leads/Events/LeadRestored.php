<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\EntityRestored;

class LeadRestored extends EntityRestored
{
    protected function getEntity(): string
    {
        return Lead::class;
    }
}
