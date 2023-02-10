<?php

declare(strict_types=1);

namespace App\Domain\Clients;

use App\Domain\Clients\Events\ClientGatewayIntegrationCreated;
use App\Domain\Clients\Models\ClientGatewayIntegration;
use Ramsey\Uuid\Uuid;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientGatewayProjector extends Projector
{
    public function onGatewayIntegrationCreated(ClientGatewayIntegrationCreated $event): void
    {
        ClientGatewayIntegration::firstOrCreate($event->payload + [
            'id' => (string) Uuid::uuid4(),
            'active' => 1,
        ]);
    }
}
