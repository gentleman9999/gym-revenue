<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserTrashed;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadTrashed extends EndUserTrashed
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
