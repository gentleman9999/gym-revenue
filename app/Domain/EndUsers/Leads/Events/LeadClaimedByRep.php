<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserClaimedByRep;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadClaimedByRep extends EndUserClaimedByRep
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
