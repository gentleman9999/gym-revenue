<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserCreated;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadCreated extends EndUserCreated
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
