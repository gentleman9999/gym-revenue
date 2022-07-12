<?php

namespace App\Reactors\Clients;

use App\Actions\Clients\Locations\UpdateLocation;
use App\Domain\Teams\Actions\CreateTeam;
use App\Imports\LocationsImport;
use App\Imports\LocationsImportWithHeader;
use App\Models\Clients\Location;
use App\StorableEvents\Clients\Locations\LocationCreated;
use App\StorableEvents\Clients\Locations\LocationsImported;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientLocationsReactor extends Reactor
{
    public function onLocationImported(LocationsImported $event)
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
            $team = CreateTeam::run([
                'name' => $event->payload['name'],
                'locations' => [
                    $event->payload['gymrevenue_id'],
                ],
                'client_id' => $event->client,
            ]);
            $location = Location::findOrFail($event->payload['id']);
            $location->default_team_id = $team->id;
            UpdateLocation::run($location->toArray());
        }
    }
}
