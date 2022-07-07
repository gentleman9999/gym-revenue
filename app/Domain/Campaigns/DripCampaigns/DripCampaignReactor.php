<?php

namespace App\Domain\Campaigns\DripCampaigns;

use App\Domain\Campaigns\DripCampaigns\Actions\PublishDripCampaign;
use App\Domain\Campaigns\DripCampaigns\Actions\UnpublishDripCampaign;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignCreated;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignUpdated;
use App\Domain\Reminders\Events\DripCampaignLaunched;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class DripCampaignReactor extends Reactor
{
    public function onDripCampaignCreated(DripCampaignCreated $event): void
    {
        $dripCampaign = DripCampaign::findOrFail($event->aggregateRootUuid());

        $this->maybePublishOrUnpublish($dripCampaign, $event);
    }

    public function onDripCampaignUpdated(DripCampaignUpdated $event): void
    {
        $dripCampaign = DripCampaign::findOrFail($event->aggregateRootUuid());

        $this->maybePublishOrUnpublish($dripCampaign, $event);
    }

    /**
     * Fires off a PublishDripCampaign or UnpublishDripCampaign action
     * if necessary.
     * @param DripCampaign $dripCampaign
     * @param DripCampaignCreated|DripCampaignUpdated $event
     * @return void
     */
    protected function maybePublishOrUnpublish(DripCampaign $dripCampaign, DripCampaignCreated|DripCampaignUpdated $event)
    {
        if (! array_key_exists('is_published', $event->payload)) {
            //is_published not even provided in the input, don't do anything
            return;
        }
        $isPublished = $event->payload['is_published'];

        if ($isPublished === $dripCampaign->is_published) {
            //no change, don't do anything
            return;
        }
        if ($isPublished) {
            PublishDripCampaign::run($dripCampaign);

            return;
        }
        UnpublishDripCampaign::run($dripCampaign);
    }

//    public function onDripCampaignLaunched(DripCampaignLaunched $event): void
//    {
//        $dripCampaign = DripCampaign::with('audience')->findOrFail($event->aggregateRootUuid());
//        //TODO:....
//    }
}
