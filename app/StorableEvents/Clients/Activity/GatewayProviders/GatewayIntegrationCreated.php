<?php

namespace App\StorableEvents\Clients\Activity\GatewayProviders;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class GatewayIntegrationCreated extends ShouldBeStored
{
    public $client;
    public $slug;
    public $type;
    public $nickname;
    public $user;

    public function __construct(string $client, string $type, string $slug, string $nickname, string $user)
    {
        $this->client = $client;
        $this->type = $type;
        $this->slug = $slug;
        $this->nickname = $nickname;
        $this->user = $user;
    }
}
