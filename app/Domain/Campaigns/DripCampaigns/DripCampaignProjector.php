<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns;

use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignCompleted;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignCreated;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDeleted;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignLaunched;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignPublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignRestored;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignTrashed;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignUnpublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignUpdated;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DripCampaignProjector extends Projector
{
    public function onDripCampaignCreated(DripCampaignCreated $event): void
    {
        $dripCampaign = (new DripCampaign())->writeable();
        unset($event->payload['gymrevenue_id']);
        unset($event->payload['days']);
        $dripCampaign->fill($event->payload);
        $dripCampaign->id        = $event->aggregateRootUuid();
        $dripCampaign->client_id = $event->payload['client_id'];
        //$dripCampaign->status = CampaignStatusEnum::DRAFT;
        $dripCampaign->save();
    }

    public function onDripCampaignUpdated(DripCampaignUpdated $event): void
    {
        DripCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onDripCampaignTrashed(DripCampaignTrashed $event): void
    {
        DripCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onDripCampaignRestored(DripCampaignRestored $event): void
    {
        DripCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onDripCampaignDeleted(DripCampaignDeleted $event): void
    {
        DripCampaign::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onDripCampaignPublished(DripCampaignPublished $event): void
    {
        DripCampaign::findOrFail($event->aggregateRootUuid())->writeable()->forceFill(['status' => CampaignStatusEnum::PENDING])->save();
    }

    public function onDripCampaignUnpublished(DripCampaignUnpublished $event): void
    {
        DripCampaign::findOrFail($event->aggregateRootUuid())->writeable()->forceFill(['status' => CampaignStatusEnum::DRAFT])->save();
    }

    public function onDripCampaignLaunched(DripCampaignLaunched $event): void
    {
        DripCampaign::findOrFail($event->aggregateRootUuid())->writeable()->update(['status' => CampaignStatusEnum::ACTIVE]);
    }

    //TODO: a drip campaign will be completed only when end_at is set and is in the past.
    public function onDripCampaignCompleted(DripCampaignCompleted $event): void
    {
        DripCampaign::findOrFail($event->aggregateRootUuid())->writeable()->update(['status' => CampaignStatusEnum::COMPLETED]);
    }
}
