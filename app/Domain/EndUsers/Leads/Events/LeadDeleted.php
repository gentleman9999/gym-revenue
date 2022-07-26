<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserDeleted;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadDeleted extends EndUserDeleted
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
