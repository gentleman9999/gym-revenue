<?php

namespace App\Domain\EndUsers\Leads\Projectors;

use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projectors\EndUserActivityProjector;

class LeadActivityProjector extends EndUserActivityProjector
{
    protected function getModel(): EndUser
    {
        return new Lead();
    }
}
