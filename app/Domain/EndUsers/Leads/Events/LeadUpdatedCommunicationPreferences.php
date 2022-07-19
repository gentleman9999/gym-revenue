<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadUpdatedCommunicationPreferences extends EndUserUpdatedCommunicationPreferences
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
