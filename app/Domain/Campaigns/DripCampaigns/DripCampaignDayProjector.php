<?php

namespace App\Domain\Campaigns\DripCampaigns;

//use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayCompleted;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayCreated;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayDeleted;
//use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayLaunched;
//use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayPublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayRestored;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayTrashed;
//use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayUnpublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayUpdated;
//use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DripCampaignDayProjector extends Projector
{
    public function onDripCampaignDayCreated(DripCampaignDayCreated $event): void
    {
        $dripCampaignDay = (new DripCampaignDay())->writeable();
        $dripCampaignDay->fill($event->payload);
        $dripCampaignDay->id = $event->aggregateRootUuid();
        $dripCampaignDay->drip_campaign_id = $event->payload['drip_campaign_id'];
        $dripCampaignDay->save();
    }

    public function onDripCampaignDayUpdated(DripCampaignDayUpdated $event): void
    {
        DripCampaignDay::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onDripCampaignDayTrashed(DripCampaignDayTrashed $event): void
    {
        DripCampaignDay::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onDripCampaignDayRestored(DripCampaignDayRestored $event): void
    {
        DripCampaignDay::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onDripCampaignDayDeleted(DripCampaignDayDeleted $event): void
    {
        DripCampaignDay::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    /*    public function onDripCampaignDayPublished(DripCampaignDayPublished $event): void
        {
            DripCampaignDay::findOrFail($event->aggregateRootUuid())->writeable()->forceFill(['status' => CampaignStatusEnum::PENDING])->save();
        }

        public function onDripCampaignDayUnpublished(DripCampaignDayUnpublished $event): void
        {
            DripCampaignDay::findOrFail($event->aggregateRootUuid())->writeable()->forceFill(['status' => CampaignStatusEnum::DRAFT])->save();
        }

        public function onDripCampaignDayLaunched(DripCampaignDayLaunched $event): void
        {
            DripCampaignDay::findOrFail($event->aggregateRootUuid())->writeable()->update(['status' => CampaignStatusEnum::ACTIVE]);
        }

        public function onDripCampaignDayCompleted(DripCampaignDayCompleted $event): void
        {
            DripCampaignDay::findOrFail($event->aggregateRootUuid())->writeable()->update(['status' => CampaignStatusEnum::COMPLETED]);
        }
    */
}
