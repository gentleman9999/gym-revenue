<?php

namespace App\StorableEvents\Clients;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PrefixCreated extends ShouldBeStored
{
    public $client, $prefix;

    public function __construct(string $client, string $prefix)
    {
        $this->client = $client;
        $this->prefix = $prefix;
    }
}
