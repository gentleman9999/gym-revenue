<?php

namespace App\Domain\LeadStatuses;

use App\Domain\LeadStatuses\Events\LeadStatusCreated;
use App\Domain\LeadStatuses\Events\LeadStatusUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LeadStatusProjector extends Projector
{
    public function onLeadStatusCreated(LeadStatusCreated $event): void
    {
        $lead_status = (new LeadStatus())->writeable();
        $lead_status->id = $event->aggregateRootUuid();
        $lead_status->client_id = $event->payload['client_id'];
        $lead_status->order = 1;
        $lead_status->fill($event->payload);
        $lead_status->save();
    }

    public function onLeadStatusUpdated(LeadStatusUpdated $event): void
    {
        $lead_status = LeadStatus::findOrFail($event->aggregateRootUuid())->writeable();
        $lead_status->updateOrFail($event->payload);
    }
}
