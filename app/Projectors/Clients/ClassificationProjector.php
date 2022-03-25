<?php

namespace App\Projectors\Clients;

use App\Models\Clients\Classification;
use App\StorableEvents\Clients\Classifications\ClassificationCreated;
use App\StorableEvents\Clients\Classifications\ClassificationDeleted;
use App\StorableEvents\Clients\Classifications\ClassificationRestored;
use App\StorableEvents\Clients\Classifications\ClassificationTrashed;
use App\StorableEvents\Clients\Classifications\ClassificationUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClassificationProjector extends Projector
{
    public function onClassificationCreated(ClassificationCreated $event)
    {
        Classification::create(
            $event->payload
        );
    }

    public function onClassificationUpdated(ClassificationUpdated $event)
    {
        Classification::findOrFail($event->payload['id'])->updateOrFail($event->payload);
    }

    public function onClassificationTrashed(ClassificationTrashed $event)
    {
        Classification::findOrFail($event->id)->delete();
    }

    public function onClassificationRestored(ClassificationRestored $event)
    {
        Classification::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onClassificationDeleted(ClassificationDeleted $event)
    {
        Classification::withTrashed()->findOrFail($event->id)->forceDelete();
    }
}
