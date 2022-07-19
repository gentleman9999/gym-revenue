<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Models\Client;
use App\StorableEvents\EntityDeleted;

class ClientDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
