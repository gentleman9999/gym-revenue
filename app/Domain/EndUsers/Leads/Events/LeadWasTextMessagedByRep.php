<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserWasTextMessagedByRep;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadWasTextMessagedByRep extends EndUserWasTextMessagedByRep
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
