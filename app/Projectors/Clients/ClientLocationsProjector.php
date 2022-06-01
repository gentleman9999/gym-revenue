<?php

namespace App\Projectors\Clients;

use App\Models\Clients\Location;
use App\Models\Clients\LocationDetails;
use App\StorableEvents\Clients\Locations\LocationCreated;
use App\StorableEvents\Clients\Locations\LocationDeleted;
use App\StorableEvents\Clients\Locations\LocationRestored;
use App\StorableEvents\Clients\Locations\LocationTrashed;
use App\StorableEvents\Clients\Locations\LocationUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientLocationsProjector extends Projector
{
    private $details = ['poc_first', 'poc_last', 'poc_phone'];

    public function onLocationCreated(LocationCreated $event)
    {

        //get only the keys we care about (the ones marked as fillable)
        $location_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Location())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $location = Location::create(
            $location_table_data
        );

        foreach ($this->details as $field) {
            LocationDetails::createOrUpdateRecord($event->payload['id'], $event->client, $field, $event->payload[$field] ?? null);
        }
    }

    public function onLocationUpdated(LocationUpdated $event)
    {
        //get only the keys we care about (the ones marked as fillable)
        $location_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Location())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        Location::findOrFail($event->payload['id'])->updateOrFail($location_table_data);

        foreach ($this->details as $field) {
            LocationDetails::createOrUpdateRecord($event->payload['id'], $event->client, $field, $event->payload[$field] ?? null);
        }
    }

    public function onLocationTrashed(LocationTrashed $event)
    {
        Location::findOrFail($event->payload['id'])->delete();
    }

    public function onLocationRestored(LocationRestored $event)
    {
        Location::withTrashed()->findOrFail($event->payload['id'])->restore();
    }

    public function onLocationDeleted(LocationDeleted $event)
    {
        Location::withTrashed()->findOrFail($event->payload['id'])->forceDelete();
    }
}
