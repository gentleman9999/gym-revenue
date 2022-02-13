<?php

namespace App\StorableEvents\Shared;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserCreated extends ShouldBeStored
{
    //entityId = ClientId for ClientUsers, TeamId for CB Users
    public $entityId, $user, $payload;

    public function __construct(string $entityId, string $user, array $payload)
    {
        $this->entityId = $entityId;
        $this->user = $user;
        $this->payload = $payload;
    }
}
