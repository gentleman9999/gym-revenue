<?php

declare(strict_types=1);

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Projections\Client;
use App\StorableEvents\EntityDeleted;

class ClientDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
