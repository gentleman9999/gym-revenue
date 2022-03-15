<?php

namespace App\StorableEvents\Clients\Locations;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LocationImported extends ShouldBeStored
{
    public $client, $user, $key;

    public function __construct(string $client, string $user, string $key)
    {
        $this->client = $client;
        $this->user = $user;
        $this->key = $key;
    }
}
