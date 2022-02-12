<?php

namespace App\Projectors\Clients;

use App\Models\Endusers\LeadSource;
use App\StorableEvents\Clients\Activity\Campaigns\ClientServiceEnabled;
use App\StorableEvents\Clients\Activity\Campaigns\LeadSourceCreated;
use App\StorableEvents\Clients\Activity\Campaigns\LeadSourceUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientLeadSourcesProjector extends Projector
{
    public function onLeadSourceUpdated(LeadSourceUpdated $event)
    {
        $leadSource = LeadSource::findOrFail($event->data['id']);
        $leadSource->name = $event->data['name'];
        $leadSource->save();
    }

    public function onLeadSourceCreated(LeadSourceCreated $event)
    {
        LeadSource::create([
            'name' => $event->data['name'],
            'client_id' => $event->client,
        ]);
    }
}
