<?php

namespace App\StorableEvents\Clients\ClientServices;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClientServicesSet extends ShouldBeStored
{
    public $client, $services, $user;

    public function __construct(string $client, array $services, int $user)
    {
        $this->client = $client;
        $this->services = $services;
        $this->user = $user;
    }
}
