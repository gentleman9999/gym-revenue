<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserWasEmailedByRep;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadWasEmailedByRep extends EndUserWasEmailedByRep
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
