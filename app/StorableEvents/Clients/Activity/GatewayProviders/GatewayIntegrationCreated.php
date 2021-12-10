<?php

namespace App\StorableEvents\Clients\Activity\GatewayProviders;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class GatewayIntegrationCreated extends ShouldBeStored
{
    public $client, $slug, $type, $nickname, $user;
    public function __construct(string $client, string $type, $slug, $nickname, $user)
    {
        $this->client = $client;
        $this->type = $type;
        $this->slug = $slug;
        $this->nickname = $nickname;
        $this->user = $user;
    }
}
