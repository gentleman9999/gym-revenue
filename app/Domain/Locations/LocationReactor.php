<?php

declare(strict_types=1);

namespace App\Domain\Locations;

use App\Domain\Locations\Actions\UpdateLocation;
use App\Domain\Locations\Events\LocationCreated;
use App\Domain\Locations\Events\LocationsImported;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Actions\CreateTeam;
use App\Imports\LocationsImport;
use App\Imports\LocationsImportWithHeader;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class LocationReactor extends Reactor
{
    public function onLocationImported(LocationsImported $event): void
    {
        $headings = (new HeadingRowImport())->toArray($event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        if (in_array($headings[0][0][0], (new Location())->getFillable())) {
            Excel::import(new LocationsImportWithHeader($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        } else {
            Excel::import(new LocationsImport($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        }
    }

    public function onLocationCreated(LocationCreated $event): void
    {
        if ($event->payload['shouldCreateTeam'] ?? false) {
            $team                      = CreateTeam::run([
                'name' => $event->payload['name'],
                'locations' => [
                    $event->payload['gymrevenue_id'],
                ],
                'client_id' => $event->clientId(),
            ]);
            $location                  = Location::findOrFail($event->aggregateRootUuid());
            $location->default_team_id = $team->id;
            UpdateLocation::run($location, $location->toArray());
        }
    }
}
