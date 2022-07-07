<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Models\Client;
use App\StorableEvents\EntityTrashed;

class ClientTrashed extends EntityTrashed
{
    protected function getEntity(): string
    {
        return Client::class;
    }
}
