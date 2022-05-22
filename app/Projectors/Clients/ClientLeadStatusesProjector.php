<?php

namespace App\Projectors\Clients;

use App\Models\Endusers\LeadStatuses;
use App\StorableEvents\Clients\Activity\Leads\LeadStatusCreated;
use App\StorableEvents\Clients\Activity\Leads\LeadStatusUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientLeadStatusesProjector extends Projector
{
    public function onLeadStatusUpdated(LeadStatusUpdated $event)
    {
        $leadSource = LeadStatuses::findOrFail($event->data['id']);
        $leadSource->status = $event->data['status'];
        $leadSource->save();
    }

    public function onLeadStatusCreated(LeadStatusCreated $event)
    {
        LeadStatuses::create([
            'status' => $event->data['status'],
            'client_id' => $event->client,
            'order' => 1,
        ]);
    }
}
