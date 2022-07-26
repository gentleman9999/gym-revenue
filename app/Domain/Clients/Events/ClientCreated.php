<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Projections\Client;
use App\StorableEvents\EntityCreated;

class ClientCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
