<?php

namespace App\Domain\Campaigns\ScheduledCampaigns;

use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignCompleted;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignCreated;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignDeleted;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignLaunched;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignPublished;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignRestored;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignTrashed;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignUnpublished;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ScheduledCampaignProjector extends Projector
{
    public function onScheduledCampaignCreated(ScheduledCampaignCreated $event): void
    {
        unset($event->payload['gymrevenue_id']);
        $scheduledCampaign = (new ScheduledCampaign())->writeable();
        $scheduledCampaign->fill($event->payload);
        $scheduledCampaign->id = $event->aggregateRootUuid();
        $scheduledCampaign->client_id = $event->payload['client_id'];
        //$scheduledCampaign->status = CampaignStatusEnum::DRAFT;
        $scheduledCampaign->save();
    }

    public function onScheduledCampaignUpdated(ScheduledCampaignUpdated $event)
    {
        ScheduledCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onScheduledCampaignTrashed(ScheduledCampaignTrashed $event)
    {
        ScheduledCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onScheduledCampaignRestored(ScheduledCampaignRestored $event)
    {
        ScheduledCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onScheduledCampaignDeleted(ScheduledCampaignDeleted $event): void
    {
        ScheduledCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onScheduledCampaignPublished(ScheduledCampaignPublished $event): void
    {
        ScheduledCampaign::findOrFail($event->aggregateRootUuid())->writeable()->forceFill(['status' => CampaignStatusEnum::PENDING])->save();
    }

    public function onScheduledCampaignUnpublished(ScheduledCampaignUnpublished $event): void
    {
        ScheduledCampaign::findOrFail($event->aggregateRootUuid())->writeable()->forceFill(['status' => CampaignStatusEnum::DRAFT])->save();
    }

    public function onScheduledCampaignLaunched(ScheduledCampaignLaunched $event): void
    {
        ScheduledCampaign::findOrFail($event->aggregateRootUuid())->writeable()->update(['status' => CampaignStatusEnum::ACTIVE]);
    }

    //TODO: a scheduled campaign will be completed when gateway reports back a sent or failed status for each recipient.
    public function onScheduledCampaignCompleted(ScheduledCampaignCompleted $event): void
    {
        ScheduledCampaign::findOrFail($event->aggregateRootUuid())->writeable()->update(['status' => CampaignStatusEnum::COMPLETED]);
    }
}
