<?php

namespace App\StorableEvents\Clients\UTM;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UtmTemplateCreated extends ShouldBeStored
{
    public $client, $user, $payload;

    public function __construct(string $client, array $payload, $user)
    {
        $this->client = $client;
        $this->payload = $payload;
        $this->user = $user;
    }
}
