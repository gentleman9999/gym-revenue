<?php

namespace App\StorableEvents\Clients\Locations;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LocationsImported extends ShouldBeStored
{
    public $client;
    public $user;
    public $key;

    public function __construct(string $client, string $user, string $key)
    {
        $this->client = $client;
        $this->user = $user;
        $this->key = $key;
    }
}
