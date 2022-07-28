<?php

namespace App\Domain\Locations;

use App\Domain\Locations\Events\LocationCreated;
use App\Domain\Locations\Events\LocationDeleted;
use App\Domain\Locations\Events\LocationRestored;
use App\Domain\Locations\Events\LocationTrashed;
use App\Domain\Locations\Events\LocationUpdated;
use App\Domain\Locations\Projections\Location;
use App\Domain\Locations\Projections\LocationDetails;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LocationProjector extends Projector
{
    private $details = ['poc_first', 'poc_last', 'poc_phone'];

    public function onLocationCreated(LocationCreated $event): void
    {
        //get only the keys we care about (the ones marked as fillable)
        $location_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Location())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $location = new Location();
        $location->id = $event->aggregateRootUuid();
        $location->client_id = $event->clientId();

        $location->fill(
            $location_table_data
        )->writeable()->save();

        foreach ($this->details as $field) {
            LocationDetails::createOrUpdateRecord($event->aggregateRootUuid(), $field, $event->payload[$field] ?? null);
        }
    }

    public function onLocationUpdated(LocationUpdated $event): void
    {
        //get only the keys we care about (the ones marked as fillable)
        $location_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Location())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        Location::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($location_table_data);

        foreach ($this->details as $field) {
            LocationDetails::createOrUpdateRecord($event->aggregateRootUuid(), $field, $event->payload[$field] ?? null);
        }
    }

    public function onLocationTrashed(LocationTrashed $event): void
    {
        Location::findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onLocationRestored(LocationRestored $event): void
    {
        Location::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onLocationDeleted(LocationDeleted $event): void
    {
        Location::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
