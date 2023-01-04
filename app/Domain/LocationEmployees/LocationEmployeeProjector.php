<?php

declare(strict_types=1);

namespace App\Domain\LocationEmployees;

use App\Domain\LocationEmployees\Events\LocationEmployeeCreated;
use App\Domain\LocationEmployees\Events\LocationEmployeeDeleted;
use App\Domain\LocationEmployees\Events\LocationEmployeeRestored;
use App\Domain\LocationEmployees\Events\LocationEmployeeTrashed;
use App\Domain\LocationEmployees\Events\LocationEmployeeUpdated;
use App\Domain\LocationEmployees\Projections\LocationEmployee;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LocationEmployeeProjector extends Projector
{
    public function onStartingEventReplay()
    {
        LocationEmployee::delete();
    }

    public function onLocationEmployeeCreated(LocationEmployeeCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $location_employees = (new LocationEmployee())->writeable();
            $location_employees->fill($event->payload);
            $location_employees->id = $event->aggregateRootUuid();
            $location_employees->save();
        });
    }

    public function onLocationEmployeeDeleted(LocationEmployeeDeleted $event): void
    {
        LocationEmployee::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onLocationEmployeeRestored(LocationEmployeeRestored $event): void
    {
        LocationEmployee::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onLocationEmployeeTrashed(LocationEmployeeTrashed $event): void
    {
        LocationEmployee::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onLocationEmployeeUpdated(LocationEmployeeUpdated $event): void
    {
        LocationEmployee::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
