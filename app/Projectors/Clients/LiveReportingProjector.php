<?php

namespace App\Projectors\Clients;

use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignCreated;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignCreated;
use App\Domain\EndUsers\Leads\Events\LeadConverted;
use App\Domain\EndUsers\Leads\Events\LeadCreated;
use App\Domain\EndUsers\Members\Events\MemberCreated;
use App\Enums\LiveReportingEnum;
use App\Models\LiveReportsByDay;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LiveReportingProjector extends Projector
{
    public function onLeadCreated(LeadCreated $event)
    {
        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
            ->whereGrLocationId($event->payload['gr_location_id'])
            ->whereEntity('lead')
            ->whereAction(LiveReportingEnum::ADDED)
            ->whereDate('date', '=', $event->createdAt())
            ->first();

        if ($record === null) {
            $record = LiveReportsByDay::create(
                [
                    'client_id' => $event->payload['client_id'],
                    'gr_location_id' => $event->payload['gr_location_id'],
                    'action' => LiveReportingEnum::ADDED,
                    'entity' => 'lead',
                    'date' => $event->createdAt(),
                ]
            );
        }

        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onMemberCreated(MemberCreated $event)
    {
        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
            ->whereGrLocationId($event->payload['gr_location_id'])
            ->whereAction(LiveReportingEnum::ADDED)
            ->whereEntity('member')
            ->whereDate('date', '=', $event->createdAt())
            ->first();

        if ($record === null) {
            $record = LiveReportsByDay::create(
                [
                    'client_id' => $event->payload['client_id'],
                    'gr_location_id' => $event->payload['gr_location_id'],
                    'action' => LiveReportingEnum::ADDED,
                    'entity' => 'member',
                    'date' => $event->createdAt(),
                ]
            );
        }
        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onLeadConverted(LeadConverted $event)
    {
        $record = LiveReportsByDay::firstOrCreate(
            [
                'client_id' => $event->data['client_id'],
                'location_id' => $event->data['gr_location_id'],
                'action' => LiveReportingEnum::CONVERTED,
                'entity' => 'lead',
                'date' => $event->createdAt(),
            ]
        );
        $record->updateOrFail(['value' => $record->value + 1]);
    }

    public function onDripCampaignCreated(DripCampaignCreated $event): void
    {
        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
            ->whereGrLocationId($event->payload['gymrevenue_id'])
            ->whereAction(LiveReportingEnum::DRIP_CAMPAIGN_STARTED)
            ->whereEntity('mass-comms')
            ->whereDate('date', '=', $event->createdAt())
            ->first();

        if ($record === null) {
            $record = LiveReportsByDay::create(
                [
                    'client_id' => $event->payload['client_id'],
                    'gr_location_id' => $event->payload['gymrevenue_id'],
                    'action' => LiveReportingEnum::DRIP_CAMPAIGN_STARTED,
                    'entity' => 'mass-comms',
                    'date' => $event->createdAt(),
                ]
            );
        }
        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onScheduledCampaignCreated(ScheduledCampaignCreated $event): void
    {
        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
            ->whereGrLocationId($event->payload['gymrevenue_id'])
            ->whereAction(LiveReportingEnum::SCHEDULED_CAMPAIGN_STARTED)
            ->whereEntity('mass-comms')
            ->whereDate('date', '=', $event->createdAt())
            ->first();

        if ($record === null) {
            $record = LiveReportsByDay::create(
                [
                    'client_id' => $event->payload['client_id'],
                    'gr_location_id' => $event->payload['gymrevenue_id'],
                    'action' => LiveReportingEnum::SCHEDULED_CAMPAIGN_STARTED,
                    'entity' => 'mass-comms',
                    'date' => $event->createdAt(),
                ]
            );
        }
        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }
}
