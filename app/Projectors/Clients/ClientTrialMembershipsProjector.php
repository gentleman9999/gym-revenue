<?php

namespace App\Projectors\Clients;

use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\StorableEvents\Clients\Activity\Campaigns\ClientServiceEnabled;
use App\StorableEvents\Clients\ClientServices\TrialMembershipTypeCreated;
use App\StorableEvents\Clients\ClientServices\TrialMembershipTypeUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientTrialMembershipsProjector extends Projector
{
    public function onTrialMembershipTypeUpdated(TrialMembershipTypeUpdated $event)
    {
        $trialMembershipType = TrialMembershipType::findOrFail($event->data['id']);
        $trialMembershipType->type_name = $event->data['type_name'];
        $trialMembershipType->slug = $event->data['slug'];
        $trialMembershipType->trial_length = $event->data['trial_length'];
        $trialMembershipType->locations = $event->data['locations'];
        $trialMembershipType->save();
    }

    public function onTrialMembershipTypeCreated(TrialMembershipTypeCreated $event)
    {
        TrialMembershipType::create([
            'type_name' => $event->data['type_name'],
            'slug' => $event->data['slug'],
            'trial_length' => $event->data['trial_length'],
            'locations' => $event->data['locations'],
            'client_id' => $event->client,
        ]);
    }
}
