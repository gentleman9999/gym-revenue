<?php

declare(strict_types=1);

namespace App\Domain\Locations;

use App\Domain\Locations\Events\LocationClosed;
use App\Domain\Locations\Events\LocationCreated;
use App\Domain\Locations\Events\LocationDeleted;
use App\Domain\Locations\Events\LocationReopened;
use App\Domain\Locations\Events\LocationUpdated;
use App\Domain\Locations\Projections\Location;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\Facades\Projectionist;

class LocationProjector extends Projector
{
    public function onStartingEventReplay()
    {
        Location::truncate();
    }
    private $details = ['poc_first', 'poc_last', 'poc_phone'];

    public function onLocationCreated(LocationCreated $event): void
    {
        //get only the keys we care about (the ones marked as fillable)
        $location_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Location())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $location = (new Location())->writeable();
        $location->id = $event->aggregateRootUuid();
        $location->client_id = $event->clientId();
        $details = [];

        if (Projectionist::isReplaying()) {
            if (count($location_table_data) != count($location_table_data, COUNT_RECURSIVE)) {
                //dd($location_table_data);
                $location_table_data['phone'] = '';
            }
        }
        $location->fill($location_table_data);

        foreach ($this->details as $field) {
            $details[$field] = $event->payload[$field] ?? null;
        }

        $location->save();
    }

    public function onLocationUpdated(LocationUpdated $event): void
    {
        //get only the keys we care about (the ones marked as fillable)
        $location_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Location())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $location = Location::findOrFail($event->aggregateRootUuid())->writeable();
        $details = $location->details ?? [];

        foreach ($this->details as $field) {
            $value = $event->payload[$field] ?? null;
            if (array_key_exists($field, $details) && is_null($value)) {
                $value = $details[$field];
            }

            $details[$field] = $value;
        }

        $location->fill($location_table_data);
        $location->details = $details;
        $location->save();
    }

    public function onLocationClosed(LocationClosed $event): void
    {
        Location::findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onLocationReopened(LocationReopened $event): void
    {
        Location::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onLocationDeleted(LocationDeleted $event): void
    {
        Location::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
