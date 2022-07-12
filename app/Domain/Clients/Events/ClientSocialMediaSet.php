<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Models\Client;
use App\StorableEvents\EntityUpdated;

class ClientSocialMediaSet extends EntityUpdated
{
    protected function getEntity(): string
    {
        return Client::class;
    }
}
