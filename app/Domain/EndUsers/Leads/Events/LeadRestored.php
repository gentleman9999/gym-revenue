<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserRestored;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadRestored extends EndUserRestored
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
