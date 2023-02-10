<?php

declare(strict_types=1);

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Projections\Client;
use App\StorableEvents\EntityTrashed;

class ClientTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Client::class;
    }
}
