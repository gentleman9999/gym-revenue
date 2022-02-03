<?php

namespace App\StorableEvents\Clients\ClientServices;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClientServiceAdded extends ShouldBeStored
{
    public $client, $feature, $slug, $active;

    public function __construct(string $client, string $feature, string $slug, bool $active)
    {
        $this->client = $client;
        $this->feature = $feature;
        $this->slug = $slug;
        $this->active = $active;
    }
}
