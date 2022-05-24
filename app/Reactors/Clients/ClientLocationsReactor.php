<?php

namespace App\Reactors\Clients;

use App\Actions\Jetstream\CreateTeam;
use App\Imports\LocationsImport;
use App\Imports\LocationsImportWithHeader;
use App\Models\Clients\Location;
use App\StorableEvents\Clients\Locations\LocationCreated;
use App\StorableEvents\Clients\Locations\LocationImported;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientLocationsReactor extends Reactor
{
    public function onLocationImported(LocationImported $event)
    {
        $headings = (new HeadingRowImport())->toArray($event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        if (in_array($headings[0][0][0], (new Location())->getFillable())) {
            Excel::import(new LocationsImportWithHeader($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        } else {
            Excel::import(new LocationsImport($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    public function onLocationCreated(LocationCreated $event)
    {
        if ($event->payload['shouldCreateTeam'] ?? false) {
            CreateTeam::run([
                'name' => $event->payload['name'],
                'locations' => [
                    $event->payload['gymrevenue_id'],
                ],
                'client_id' => $event->client,
            ]);
        }
    }
}
