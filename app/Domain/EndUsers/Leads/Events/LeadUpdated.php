<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserUpdated;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadUpdated extends EndUserUpdated
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
