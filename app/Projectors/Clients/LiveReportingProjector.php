<?php

namespace App\Projectors\Clients;

use App\Domain\Leads\Events\LeadConverted;
use App\Domain\Leads\Events\LeadCreated;
use App\Models\LiveReportsByDay;
use App\StorableEvents\Endusers\Members\MemberCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LiveReportingProjector extends Projector
{
    public function onLeadCreated(LeadCreated $event)
    {
        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
            ->whereGrLocationId($event->payload['gr_location_id'])
            ->whereEntity('lead')
            ->whereDate('date', '=', $event->createdAt())
            ->first();

        if ($record === null) {
            $record = LiveReportsByDay::create(
                [
                    'client_id' => $event->payload['client_id'],
                    'gr_location_id' => $event->payload['gr_location_id'],
                    'entity' => 'lead',
                    'date' => $event->createdAt(),
                ]
            );
        }

        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onMemberCreated(MemberCreated $event)
    {
        $record = LiveReportsByDay::whereClientId($event->data['client_id'])
            ->whereGrLocationId($event->data['location_id'])
            ->whereEntity('member')
            ->whereDate('date', '=', $event->createdAt())
            ->first();

        if ($record === null) {
            $record = LiveReportsByDay::create(
                [
                    'client_id' => $event->data['client_id'],
                    'gr_location_id' => $event->data['gr_location_id'],
                    'entity' => 'member',
                    'date' => $event->createdAt(),
                ]
            );
        }
        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onLeadConverted(LeadConverted $event)
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
