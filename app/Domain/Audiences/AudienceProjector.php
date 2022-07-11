<?php

namespace App\Domain\Audiences;

use App\Domain\Audiences\Events\AudienceCreated;
use App\Domain\Audiences\Events\AudienceTrashed;
use App\Domain\Audiences\Events\AudienceUpdated;
use App\Domain\Audiences\Events\PositionDeleted;
use App\Domain\Audiences\Events\PositionRestored;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AudienceProjector extends Projector
{
    public function onAudienceCreated(AudienceCreated $event): void
    {
        $audience = (new Audience())->writeable();
        $audience->fill($event->payload);
        $audience->id = $event->aggregateRootUuid();
        $audience->client_id = $event->payload['client_id'];
        $audience->save();
    }

    public function onAudienceUpdated(AudienceUpdated $event)
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onAudienceTrashed(AudienceTrashed $event)
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onAudienceRestored(PositionRestored $event)
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onAudienceDeleted(PositionDeleted $event): void
    {
        Audience::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
