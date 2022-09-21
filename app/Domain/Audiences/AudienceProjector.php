<?php

namespace App\Domain\Audiences;

use App\Domain\Audiences\Events\AudienceCreated;
use App\Domain\Audiences\Events\AudienceDeleted;
use App\Domain\Audiences\Events\AudienceRestored;
use App\Domain\Audiences\Events\AudienceTrashed;
use App\Domain\Audiences\Events\AudienceUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AudienceProjector extends Projector
{
    public function onStartingEventReplay()
    {
        Audience::truncate();
    }

    public function onAudienceCreated(AudienceCreated $event): void
    {
        $audience = (new Audience())->writeable();
        $audience->fill($event->payload);
        $audience->id = $event->aggregateRootUuid();
        $audience->client_id = $event->payload['client_id'];
        $audience->save();
    }

    public function onAudienceUpdated(AudienceUpdated $event): void
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }

    public function onAudienceTrashed(AudienceTrashed $event): void
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onAudienceRestored(AudienceRestored $event): void
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onAudienceDeleted(AudienceDeleted $event): void
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
