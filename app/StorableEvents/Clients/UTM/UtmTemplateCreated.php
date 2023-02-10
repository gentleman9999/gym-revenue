<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\UTM;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UtmTemplateCreated extends ShouldBeStored
{
    public $client;
    public $user;
    public $payload;

    public function __construct(string $client, array $payload, string $user)
    {
        $this->client  = $client;
        $this->payload = $payload;
        $this->user    = $user;
    }
}
