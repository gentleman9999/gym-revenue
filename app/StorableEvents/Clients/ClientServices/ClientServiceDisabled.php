<?php

namespace App\StorableEvents\Clients\ClientServices;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClientServiceDisabled extends ShouldBeStored
{
    public $client, $slug, $user;

    public function __construct(string $client, string $slug, int $user)
    {
        $this->client = $client;
        $this->slug = $slug;
        $this->user = $user;
    }
}
