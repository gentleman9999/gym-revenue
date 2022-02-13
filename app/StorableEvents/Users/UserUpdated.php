<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

//entityId = ClientId for ClientUsers, TeamId for CB Users
class UserUpdated extends ShouldBeStored
{
    public $id, $user, $payload;

    public function __construct(string $id, string $user, array $payload)
    {
        $this->id = $id;
        $this->user = $user;
        $this->payload = $payload;
    }
}
