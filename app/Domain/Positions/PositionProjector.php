<?php

namespace App\Domain\Positions;

use App\Domain\Audiences\Events\AudienceTrashed;
use App\Domain\Positions\Events\PositionCreated;
use App\Domain\Positions\Events\PositionDeleted;
use App\Domain\Positions\Events\PositionRestored;
use App\Domain\Positions\Events\PositionUpdated;
use App\Models\Position;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PositionProjector extends Projector
{
    public function onPositionCreated(PositionCreated $event): void
    {
        $position = (new Position());
        $position->fill($event->payload);
        $position->id = $event->aggregateRootUuid();
        $position->client_id = $event->clientId();
        $position->save();
    }

    public function onPositionUpdated(PositionUpdated $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->updateOrFail($event->payload);
    }

    public function onPositionTrashed(AudienceTrashed $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onPositionRestored(PositionRestored $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onPositionDeleted(PositionDeleted $event): void
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
