<?php

declare(strict_types=1);

namespace App\Projectors\Clients;

use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignCreated;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignCreated;
use App\Domain\Users\Events\EndUserConverted;
use App\Domain\Users\Events\UserCreated;
use App\Enums\LiveReportingEnum;
use App\Models\LiveReportsByDay;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LiveReportingProjector extends Projector
{
    /** @TODO: When Implementing, check if the created user is of lead type */
    public function onLeadCreated(UserCreated $event): void
    {
//        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
//            ->whereGrLocationId($event->payload['gr_location_id'])
//            ->whereEntity('lead')
//            ->whereAction(LiveReportingEnum::ADDED)
//            ->whereDate('date', '=', $event->createdAt())
//            ->first();
//
//        if ($record === null) {
//            $record = LiveReportsByDay::create(
//                [
//                    'client_id' => $event->payload['client_id'],
//                    'gr_location_id' => $event->payload['gr_location_id'],
//                    'action' => LiveReportingEnum::ADDED,
//                    'entity' => 'lead',
//                    'date' => $event->createdAt(),
//                ]
//            );
//        }
//
//        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    /** @TODO: When Implementing, check if the created user is of member type */
    public function onMemberCreated(UserCreated $event): void
    {
//        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
//            ->whereGrLocationId($event->payload['gr_location_id'])
//            ->whereAction(LiveReportingEnum::ADDED)
//            ->whereEntity('member')
//            ->whereDate('date', '=', $event->createdAt())
//            ->first();
//
//        if ($record === null) {
//            $record = LiveReportsByDay::create(
//                [
//                    'client_id' => $event->payload['client_id'],
//                    'gr_location_id' => $event->payload['gr_location_id'],
//                    'action' => LiveReportingEnum::ADDED,
//                    'entity' => 'member',
//                    'date' => $event->createdAt(),
//                ]
//            );
//        }
//        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onLeadConverted(EndUserConverted $event): void
    {
//        $record = LiveReportsByDay::firstOrCreate(
//            [
//                'client_id' => $event->data['client_id'],
//                'location_id' => $event->data['gr_location_id'],
//                'action' => LiveReportingEnum::CONVERTED,
//                'entity' => 'lead',
//                'date' => $event->createdAt(),
//            ]
//        );
//        $record->updateOrFail(['value' => $record->value + 1]);
    }

    public function onDripCampaignCreated(DripCampaignCreated $event): void
    {
//        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
//            ->whereGrLocationId($event->payload['gymrevenue_id'])
//            ->whereAction(LiveReportingEnum::DRIP_CAMPAIGN_STARTED)
//            ->whereEntity('mass-comms')
//            ->whereDate('date', '=', $event->createdAt())
//            ->first();
//
//        if ($record === null) {
//            $record = LiveReportsByDay::create(
//                [
//                    'client_id' => $event->payload['client_id'],
//                    'gr_location_id' => $event->payload['gymrevenue_id'],
//                    'action' => LiveReportingEnum::DRIP_CAMPAIGN_STARTED,
//                    'entity' => 'mass-comms',
//                    'date' => $event->createdAt(),
//                ]
//            );
//        }
//        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }

    public function onScheduledCampaignCreated(ScheduledCampaignCreated $event): void
    {
//        $record = LiveReportsByDay::whereClientId($event->payload['client_id'])
//            ->whereGrLocationId($event->payload['gymrevenue_id'])
//            ->whereAction(LiveReportingEnum::SCHEDULED_CAMPAIGN_STARTED)
//            ->whereEntity('mass-comms')
//            ->whereDate('date', '=', $event->createdAt())
//            ->first();
//
//        if ($record === null) {
//            $record = LiveReportsByDay::create(
//                [
//                    'client_id' => $event->payload['client_id'],
//                    'gr_location_id' => $event->payload['gymrevenue_id'],
//                    'action' => LiveReportingEnum::SCHEDULED_CAMPAIGN_STARTED,
//                    'entity' => 'mass-comms',
//                    'date' => $event->createdAt(),
//                ]
//            );
//        }
//        $record->updateOrFail(['value' => (float)$record->value + 1]);
    }
}
