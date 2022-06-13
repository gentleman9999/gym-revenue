<?php

namespace App\StorableEvents\Clients\Comms;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EmailTemplateThumbnailUpdated extends ShouldBeStored
{
    public $client;
    public $id;
    public $key;
    public $url;

    public function __construct(string $client, string $id, string $key, string $url)
    {
        $this->client = $client;
        $this->id = $id;
        $this->key = $key;
        $this->url = $url;
    }
}
