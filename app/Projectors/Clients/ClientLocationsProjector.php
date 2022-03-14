<?php

namespace App\Projectors\Clients;

use App\Imports\LocationsImport;
use App\Models\Clients\Location;
use App\Models\Clients\LocationDetails;
use App\StorableEvents\Clients\Locations\LocationCreated;
use App\StorableEvents\Clients\Locations\LocationImported;
use App\StorableEvents\Clients\Locations\LocationDeleted;
use App\StorableEvents\Clients\Locations\LocationRestored;
use App\StorableEvents\Clients\Locations\LocationTrashed;
use App\StorableEvents\Clients\Locations\LocationUpdated;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Bouncer;

class ClientLocationsProjector extends Projector
{
    private $details = ['phone', 'poc_first', 'poc_last', 'poc_phone', 'open_date', 'close_date'];

    public function onLocationCreated(LocationCreated $event)
    {
        $location = Location::create(
            $event->payload
        );

        foreach ($this->details as $field) {
            LocationDetails::createOrUpdateRecord($event->payload['id'], $event->client, $field, $event->payload[$field] ?? null);
        }
    }

    public function onLocationImported(LocationImported $event)
    {
        Excel::import(new LocationsImport($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
    }

    public function onLocationUpdated(LocationUpdated $event)
    {
        Location::findOrFail($event->payload['id'])->updateOrFail($event->payload);

        foreach ($this->details as $field) {
            LocationDetails::createOrUpdateRecord($event->payload['id'],$event->client, $field, $event->payload[$field] ?? null);
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
