<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserWasCalledByRep;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadWasCalledByRep extends EndUserWasCalledByRep
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
