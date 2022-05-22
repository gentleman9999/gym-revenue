<?php

namespace App\StorableEvents\Clients\ShortUrl;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ShortUrlCreated extends ShouldBeStored
{
    public $user;
    public $data;
    public $client;

    public function __construct(string $client, string $user, array $data)
    {
        $this->client = $client;
        $this->user = $user;
        $this->data = $data;
    }
}
