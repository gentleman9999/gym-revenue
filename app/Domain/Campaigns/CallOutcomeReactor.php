<?php

declare(strict_types=1);

namespace App\Domain\Campaigns;

use App\Domain\Campaigns\Events\CallOutcomeCreated;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class CallOutcomeReactor extends Reactor
{
    public function onCallOutcomeCreated(CallOutcomeCreated $event): void
    {
    }

    public function onCallOutcomeUpdated(CallOutcomeUpdated $event): void
    {
        $callOutcome = CallOutcome::findOrFail($event->aggregateRootUuid());

        $this->maybePublishOrUnpublish($callOutcome, $event);
    }

    /**
     * Fires off a PublishCallOutcome or UnpublishCallOutcome action
     * if necessary.
     * @param CallOutcome $CallOutcome
     */
    protected function maybePublishOrUnpublish(CallOutcome $callOutcome, CallOutcomeCreated|CallOutcomeUpdated $event): void
    {
        if (! array_key_exists('is_published', $event->payload)) {
            //is_published not even provided in the input, don't do anything
            return;
        }
        $isPublished = $event->payload['is_published'];

        if ($isPublished === $callOutcome->is_published) {
            //no change, don't do anything
            return;
        }
        if ($isPublished) {
            PublishCallOutcome::run($callOutcome);

            return;
        }
        UnpublishCallOutcome::run($callOutcome);
    }

//    public function onCallOutcomeLaunched(CallOutcomeLaunched $event): void
//    {
//        $CallOutcome = CallOutcome::with('audience')->findOrFail($event->aggregateRootUuid());
//        //TODO:....
//    }
}
