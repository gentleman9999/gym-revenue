<?php

namespace App\StorableEvents\Clients\Comms;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SMSTemplateCreated extends ShouldBeStored
{
    public $client;
    public $template;
    public $created;

    public function __construct(string $client, string $template, string $created)
    {
        $this->client = $client;
        $this->template = $template;
        $this->created = $created;
    }
}
