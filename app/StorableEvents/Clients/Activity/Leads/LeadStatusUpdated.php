<?php

namespace App\StorableEvents\Clients\Activity\Leads;

use App\StorableEvents\Clients\Activity\Campaigns\LeadSourceUpdated;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadStatusUpdated extends LeadSourceUpdated
{
}
