<?php

namespace App\Domain\EndUsers\Leads\Projectors;

use App\Domain\EndUsers\Leads\Events\LeadConverted;
use App\Domain\EndUsers\Leads\Events\TrialMembershipAdded;
use App\Domain\EndUsers\Leads\Events\TrialMembershipUsed;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Leads\Projections\LeadDetails;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projectors\EndUserCrudProjector;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\TrialMembership;

class LeadCrudProjector extends EndUserCrudProjector
{
    protected function getModel(): EndUser
    {
        return new Lead();
    }

    public function onTrialMembershipAdded(TrialMembershipAdded $event)
    {
        $lead = Lead::findOrFail($event->aggregateRootUuid());
        $trial = TrialMembershipType::find($event->trial);
        $client_id = $lead->client_id;
        TrialMembership::create([
            'client_id' => $client_id,
            'type_id' => $event->trial,
            'lead_id' => $lead->id,
            'start_date' => $event->createdAt(),
            'expiry_date' => $event->createdAt()->addDays($trial->trial_length),
            'location_id' => $lead->gr_location_id,
            'active' => 1,
        ]);

        $trail_started = new LeadDetails();
        $trail_started->forceFill([
            'client_id' => $client_id,
            'lead_id' => $lead->id,
            'field' => 'trial-started',
            'value' => $event->createdAt(),
            'misc' => ['trial_id' => $event->trial, 'date' => $event->createdAt(), 'client' => $client_id],
        ]);
        $trail_started->save();
    }

    public function onTrialMembershipUsed(TrialMembershipUsed $event)
    {
        $trial_used = new LeadDetails();
        $trial_used->forceFill([
            'client_id' => $event->clientId(),
            'lead_id' => $event->aggregateRootUuid(),
            'field' => 'trial-used',
            'value' => $event->trial,
            'misc' => ['trial_id' => $event->trial, 'date' => $event->createdAt(), 'client' => $event->clientId()],
        ]);
        $trial_used->save();
    }

    public function onLeadConverted(LeadConverted $event)
    {
        $record = lead::withTrashed()->findOrFail($event->aggregateRootUuid());
        $record->forceFill(['converted_at' => $event->createdAt(), 'member_id' => $event->member])->save();
    }
}
