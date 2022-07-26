<?php

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Projections\Client;
use App\StorableEvents\EntityUpdated;

class ClientLogoUploaded extends EntityUpdated
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
