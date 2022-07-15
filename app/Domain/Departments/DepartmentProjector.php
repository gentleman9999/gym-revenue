<?php

namespace App\Domain\Departments;

use App\Domain\Departments\Events\DepartmentCreated;
use App\Domain\Departments\Events\DepartmentDeleted;
use App\Domain\Departments\Events\DepartmentRestored;
use App\Domain\Departments\Events\DepartmentTrashed;
use App\Domain\Departments\Events\DepartmentUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DepartmentProjector extends Projector
{
    public function onPositionCreated(DepartmentCreated $event): void
    {
        $position = (new Department());
        $position->fill($event->payload);
        $position->id = $event->aggregateRootUuid();
        $position->client_id = $event->clientId();
        $position->save();
    }

    public function onDepartmentUpdated(DepartmentUpdated $event)
    {
        Department::withTrashed()->findOrFail($event->aggregateRootUuid())->updateOrFail($event->payload);
    }

    public function onDepartmentTrashed(DepartmentTrashed $event)
    {
        Department::withTrashed()->findOrFail($event->aggregateRootUuid())->delete();
    }

    public function onDepartmentRestored(DepartmentRestored $event)
    {
        Department::withTrashed()->findOrFail($event->aggregateRootUuid())->restore();
    }

    public function onDepartmentDeleted(DepartmentDeleted $event): void
    {
        Department::withTrashed()->findOrFail($event->aggregateRootUuid())->forceDelete();
    }
}
