<?php

namespace App\Domain\LeadSources;

use App\Domain\LeadSources\Events\LeadSourceCreated;
use App\Domain\LeadSources\Events\LeadSourceUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LeadSourcesProjector extends Projector
{
    public function onLeadSourceCreated(LeadSourceCreated $event)
    {
        $lead_source = (new LeadSource())->writeable();
        $lead_source->id = $event->aggregateRootUuid();
        $lead_source->client_id = $event->payload['client_id'];
        $lead_source->fill($event->payload);
        $lead_source->save();
    }

    public function onLeadSourceUpdated(LeadSourceUpdated $event)
    {
        $leadSource = LeadSource::findOrFail($event->aggregateRootUuid())->writeable();
        $leadSource->updateOrFail($event->payload);
    }
}
