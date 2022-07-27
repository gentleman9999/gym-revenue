<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Models\Client;
use App\StorableEvents\EntityUpdated;

class ClientGatewaySet extends EntityUpdated
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
