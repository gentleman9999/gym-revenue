<?php

namespace App\StorableEvents\Clients\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserCreated extends ShouldBeStored
{
    //user = request's current_user
    public $client, $user, $payload;

    public function __construct(string $client, string $user, array $payload)
    {
        $this->client = $client;
        $this->user = $user;
        $this->payload = $payload;
    }
}
