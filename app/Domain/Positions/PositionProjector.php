<?php

namespace App\Domain\Positions;

use App\Domain\Audiences\Events\AudienceTrashed;
use App\Domain\Audiences\Events\PositionDeleted;
use App\Domain\Audiences\Events\PositionRestored;
use App\Domain\Positions\Events\PositionCreated;
use App\Domain\Positions\Events\PositionUpdated;
use App\Models\Position;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PositionProjector extends Projector
{
    public function onPositionCreated(PositionCreated $event): void
    {
        $position = (new Position())->writeable();
        $position->fill($event->payload);
        $position->id = $event->aggregateRootUuid();
        $position->client_id = $event->payload['client_id'];
        $position->save();
    }

    public function oPositionUpdated(PositionUpdated $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onAudienceTrashed(AudienceTrashed $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onAudienceRestored(PositionRestored $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onAudienceDeleted(PositionDeleted $event): void
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
