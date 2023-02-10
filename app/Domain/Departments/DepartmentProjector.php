<?php

declare(strict_types=1);

namespace App\Domain\Departments;

use App\Domain\Departments\Events\DepartmentCreated;
use App\Domain\Departments\Events\DepartmentDeleted;
use App\Domain\Departments\Events\DepartmentRestored;
use App\Domain\Departments\Events\DepartmentTrashed;
use App\Domain\Departments\Events\DepartmentUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DepartmentProjector extends Projector
{
    public function onDepartmentCreated(DepartmentCreated $event): void
    {
        $dept = (new Department());
        $dept->fill($event->payload);
        $dept->id        = $event->aggregateRootUuid();
        $dept->client_id = $event->clientId();
        $dept->save();

        if (array_key_exists('positions', $event->payload)) {
            $dept->positions()->sync($event->payload['positions']);
        }
    }

    public function onDepartmentUpdated(DepartmentUpdated $event): void
    {
        $dept = Department::withTrashed()->findOrFail($event->aggregateRootUuid());

        $dept->updateOrFail($event->payload);

        if (array_key_exists('positions', $event->payload)) {
            $dept->positions()->sync($event->payload['positions']);
        }
    }

    public function onDepartmentTrashed(DepartmentTrashed $event): void
    {
        Department::withTrashed()->findOrFail($event->aggregateRootUuid())->delete();
    }

    public function onDepartmentRestored(DepartmentRestored $event): void
    {
        Department::withTrashed()->findOrFail($event->aggregateRootUuid())->restore();
    }

    public function onDepartmentDeleted(DepartmentDeleted $event): void
    {
        Department::withTrashed()->findOrFail($event->aggregateRootUuid())->forceDelete();
    }
}
