<?php

namespace App\Projectors\Clients;

use App\Models\Clients\ClientDetail;
use App\Models\Clients\Features\ClientService;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\StorableEvents\Clients\Activity\Campaigns\ClientServiceEnabled;
use App\StorableEvents\Clients\ClientServices\ClientServiceAdded;
use App\StorableEvents\Clients\ClientServices\ClientServiceDisabled;
use App\StorableEvents\Clients\ClientServices\ClientServicesSet;
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
}
