<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Models\Client;
use App\StorableEvents\GymRevCrudEvent;

class ClientServicesSet extends GymRevCrudEvent
{
    public array $services;

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    protected function getEntity(): string
    {
        return Client::class;
    }

    protected function getOperation(): string
    {
        return "SERVICES_SET";
    }
}
