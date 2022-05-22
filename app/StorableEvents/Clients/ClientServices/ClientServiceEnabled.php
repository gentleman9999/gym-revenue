<?php

namespace App\StorableEvents\Clients\ClientServices;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClientServiceEnabled extends ShouldBeStored
{
    public $client;
    public $slug;
    public $user;

    public function __construct(string $client, string $slug, int $user)
    {
        $this->client = $client;
        $this->slug = $slug;
        $this->user = $user;
    }
}
