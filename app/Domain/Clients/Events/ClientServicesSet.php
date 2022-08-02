<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Projections\Client;
use App\StorableEvents\GymRevCrudEvent;

class ClientServicesSet extends GymRevCrudEvent
{
    public array $services;

    public function __construct(array $services)
    {
        $this->services = $services;
        parent::__construct();
    }

    public function getEntity(): string
    {
        return Client::class;
    }

    protected function getOperation(): string
    {
        return "SERVICES_SET";
    }
}
