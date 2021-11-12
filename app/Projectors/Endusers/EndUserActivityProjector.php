<?php

namespace App\Projectors\Endusers;

use App\Models\Endusers\Lead;
use App\Models\Endusers\LeadDetails;
use App\Models\User;
use App\StorableEvents\Endusers\NewLeadMade;
use App\StorableEvents\Endusers\UpdateLead;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EndUserActivityProjector extends Projector
{
    public function onNewLeadMade(NewLeadMade $event)
    {
        $lead = Lead::create($event->lead);

    }

    public function onUpdateLead(UpdateLead $event)
    {
        $lead = Lead::findOrFail($event->id);
        $old_data = $lead->toArray();
        $user = User::find($event->user);
        $lead->updateOrFail($event->lead);
        LeadDetails::create([
            'lead_id' => $event->id,
            'client_id' => $lead->client_id,
            'field' => 'updated',
            'value' => $user->email,
            'misc'  => [
                'old_data' => $old_data,
                'new_data' => $event->lead,
                'user' => $event->user
            ]
        ]);
    }
}
