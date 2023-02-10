<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns;

//use App\Domain\Campaigns\DripCampaigns\Actions\PublishDripCampaign;
//use App\Domain\Campaigns\DripCampaigns\Actions\UnpublishDripCampaign;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayCreated;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayUpdated;
//use App\Domain\Reminders\Events\DripCampaignDayLaunched;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class DripCampaignDayReactor extends Reactor
{
    public function onDripCampaignDayCreated(DripCampaignDayCreated $event): void
    {
        $dripCampaign = DripCampaignDay::findOrFail($event->aggregateRootUuid());

        //$this->maybePublishOrUnpublish($dripCampaign, $event);
    }

    public function onDripCampaignDayUpdated(DripCampaignDayUpdated $event): void
    {
        $dripCampaign = DripCampaignDay::findOrFail($event->aggregateRootUuid());

        //$this->maybePublishOrUnpublish($dripCampaign, $event);
    }

//    /**
//     * Fires off a PublishDripCampaign or UnpublishDripCampaign action
//     * if necessary.
//     * @param DripCampaignDay $dripCampaignDay
//     * @param DripCampaignDayCreated|DripCampaignDayUpdated $event
//     * @return void
//     */
//    protected function maybePublishOrUnpublish(DripCampaignDay $dripCampaignDay, DripCampaignDayCreated|DripCampaignDayUpdated $event): void
//    {
//        if (! array_key_exists('is_published', $event->payload)) {
//            //is_published not even provided in the input, don't do anything
//            return;
//        }
//        $isPublished = $event->payload['is_published'];
//
//        if ($isPublished === $dripCampaignDay->is_published) {
//            //no change, don't do anything
//            return;
//        }
//        if ($isPublished) {
//            PublishDripCampaignDay::run($dripCampaignDay);
//
//            return;
//        }
//        UnpublishDripCampaignDay::run($dripCampaignDay);
//    }

//    public function onDripCampaignLaunched(DripCampaignLaunched $event): void
//    {
//        $dripCampaign = DripCampaign::with('audience')->findOrFail($event->aggregateRootUuid());
//        //TODO:....
//    }
}
