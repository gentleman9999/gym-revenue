<?php

namespace App\Domain\Campaigns;

use App\Domain\Campaigns\Events\CallOutcomeCreated;
use App\Domain\Campaigns\Events\CallOutcomeUpdated;
use App\Domain\EndUsers\Projections\EndUserDetails;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CallOutcomeProjector extends Projector
{
    public function onCallOutcomeCreated(CallOutcomeCreated $event): void
    {
        $client_id = $event->payload['client_id'];
        if (count($event->payload['lead_attendees']) > 0) {
            $call_outcome = (new EndUserDetails())->writeable();
            $call_outcome->forceFill([
                'client_id' => $client_id,
                'end_user_id' => $event->payload['lead_attendees'][0],
                'field' => 'call_outcome',
                'value' => $event->payload['outcome'],
                'entity_id' => $event->payload['id'],
            ]);
        }

        if (count($event->payload['member_attendees']) > 0) {
            $call_outcome = (new EndUserDetails())->writeable();
            $call_outcome->forceFill([
                'client_id' => $client_id,
                'end_user_id' => $event->payload['member_attendees'][0],
                'field' => 'call_outcome',
                'value' => $event->payload['outcome'],
                'entity_id' => $event->payload['id'],
            ]);
        }

        $call_outcome->id = $event->aggregateRootUuid();

        $call_outcome->save();
    }

    public function onCallOutcomeUpdated(CallOutcomeUpdated $event): void
    {
        if (count($event->payload['lead_attendees']) > 0) {
            $details = EndUserDetails::findOrFail($event->payload['outcomeId'])->writeable();
            $details->lead_id = $event->payload['lead_attendees'][0];
            $details->field = 'call_outcome';
            $details->value = $event->payload['outcome'];
            $details->updateOrFail();
        }

        if (count($event->payload['member_attendees']) > 0) {
            $details = EndUserDetails::findOrFail($event->payload['outcomeId'])->writeable();
            $details->member_id = $event->payload['member_attendees'][0];
            $details->field = 'call_outcome';
            $details->value = $event->payload['outcome'];
            $details->updateOrFail();
        }
    }
}
