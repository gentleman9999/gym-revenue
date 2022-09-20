<?php

declare(strict_types=1);

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Models\ClientGatewayIntegration;
use App\StorableEvents\EntityCreated;

class ClientGatewayIntegrationCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return ClientGatewayIntegration::class;
    }
}
