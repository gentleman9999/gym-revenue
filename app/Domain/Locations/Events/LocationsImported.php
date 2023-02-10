<?php

declare(strict_types=1);

namespace App\Domain\Locations\Events;

use App\Domain\Locations\Projections\Location;
use App\StorableEvents\GymRevCrudEvent;

class LocationsImported extends GymRevCrudEvent
{
    public string $key;
    public string $client;

    public function __construct(string $key, string $client)
    {
        parent::__construct();
        $this->key    = $key;
        $this->client = $client;
    }

    public function getEntity(): string
    {
        return Location::class;
    }

    protected function getOperation(): string
    {
        return "IMPORTED";
    }
}
