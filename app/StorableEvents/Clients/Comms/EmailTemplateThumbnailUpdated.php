<?php

namespace App\StorableEvents\Clients\Comms;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EmailTemplateThumbnailUpdated extends ShouldBeStored
{
    public $client;
    public $id;
    public $thumbnail;

    public function __construct(string $client, string $id, string $thumbnail)
    {
        $this->client = $client;
        $this->id = $id;
        $this->thumbnail = $thumbnail;
    }
}
