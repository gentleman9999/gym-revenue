<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities;

use App\Domain\GymAmenities\Events\GymAmenityCreated;
use App\Domain\GymAmenities\Events\GymAmenityDeleted;
use App\Domain\GymAmenities\Events\GymAmenityRestored;
use App\Domain\GymAmenities\Events\GymAmenityTrashed;
use App\Domain\GymAmenities\Events\GymAmenityUpdated;
use App\Domain\GymAmenities\Projections\GymAmenity;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class GymAmenityProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        GymAmenity::delete();
    }

    public function onGymAmenityCreated(GymAmenityCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $gym_amenity = (new GymAmenity())->writeable();
            $gym_amenity->fill($event->payload);
            $gym_amenity->id = $event->aggregateRootUuid();
            $gym_amenity->save();
        });
    }

    public function onGymAmenityDeleted(GymAmenityDeleted $event): void
    {
        GymAmenity::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onGymAmenityRestored(GymAmenityRestored $event): void
    {
        GymAmenity::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onGymAmenityTrashed(GymAmenityTrashed $event): void
    {
        GymAmenity::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onGymAmenityUpdated(GymAmenityUpdated $event): void
    {
        GymAmenity::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
