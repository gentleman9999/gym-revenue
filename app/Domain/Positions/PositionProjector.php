<?php

namespace App\Domain\Positions;

use App\Domain\Positions\Events\PositionCreated;
use App\Domain\Positions\Events\PositionDeleted;
use App\Domain\Positions\Events\PositionRestored;
use App\Domain\Positions\Events\PositionTrashed;
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

        if (array_key_exists('departments', $event->payload)) {
            $position->departments()->sync($event->payload['departments']);
        }
    }

    public function onPositionUpdated(PositionUpdated $event)
    {
        $pos = Position::withTrashed()->findOrFail($event->aggregateRootUuid());

        $pos->updateOrFail($event->payload);

        if (array_key_exists('departments', $event->payload)) {
            $pos->departments()->sync($event->payload['departments']);
        }
    }

    public function onPositionTrashed(PositionTrashed $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->delete();
    }

    public function onPositionRestored(PositionRestored $event)
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->restore();
    }

    public function onPositionDeleted(PositionDeleted $event): void
    {
        Position::withTrashed()->findOrFail($event->aggregateRootUuid())->forceDelete();
    }
}
