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

        $location = new Location();
        $location->id = $event->aggregateRootUuid();
        $location->client_id = $event->clientId();

        if (Projectionist::isReplaying()) {
            if (count($location_table_data) != count($location_table_data, COUNT_RECURSIVE)) {
                //dd($location_table_data);
                $location_table_data['phone'] = '';
            }
        }
        $location->fill(
            $location_table_data
        )->writeable()->save();

        foreach ($this->details as $field) {
            $location->addOrUpdateDetails($field, $event->payload[$field] ?? null);
        }
    }

    public function onLocationUpdated(LocationUpdated $event): void
    {
        //get only the keys we care about (the ones marked as fillable)
        $location_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Location())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $location = Location::findOrFail($event->aggregateRootUuid());

        foreach ($this->details as $field) {
            $location->addOrUpdateDetails($field, $event->payload[$field] ?? null);
        }

        $location->writeable()->updateOrFail($location_table_data);
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
