<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Models\Client;
use App\StorableEvents\EntityRestored;

class ClientRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
