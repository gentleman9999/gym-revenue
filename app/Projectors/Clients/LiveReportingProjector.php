<?php

namespace App\Projectors\Clients;

use App\Models\LiveReportsByDay;
use App\StorableEvents\Endusers\Leads\LeadCreated;
use App\StorableEvents\Endusers\Leads\LeadUpdated;
use App\StorableEvents\Endusers\Members\MemberCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LiveReportingProjector extends Projector
{
    public function onLeadCreated(LeadCreated $event)
    {
        $test = 0;
        $record = LiveReportsByDay::firstOrCreate(
            [
                'client_id' => $event->data['client_id'],
                'location_id' => $event->data['location_id'],
                'entity' => 'lead',
                'date' => $event->createdAt(),
            ]
        );

        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onMemberCreated(MemberCreated $event)
    {
        $test = 0;
        $record = LiveReportsByDay::firstOrCreate(
            [
                'client_id' => $event->data['client_id'],
                'location_id' => $event->data['location_id'],
                'entity' => 'member',
                'date' => $event->createdAt(),
            ]
        );
        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onLeadUpdated(LeadUpdated $event)
    {
        /* TODO finish this
        $record = LiveReportsByDay::firstOrCreate(
            [
                'client_id' => $event->data['client_id'],
                'location_id' => $event->data['location_id'],
                'entity' => 'lead',
                'date' => $event->createdAt(),
            ]
        );
        $record->updateOrFail(['value' => $record->value + 1]);
        */
    }
}
