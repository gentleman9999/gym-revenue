<?php

declare(strict_types=1);

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Projections\Client;
use App\StorableEvents\EntityCreated;

class ClientOwnedEntityCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
