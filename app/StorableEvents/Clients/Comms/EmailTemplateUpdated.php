<?php

namespace App\StorableEvents\Clients\Comms;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EmailTemplateUpdated extends ShouldBeStored
{
    public $client, $template, $updated, $old, $new;
    public function __construct(string $client, string $template, string $updated, array $old, array $new)
    {
        $this->client = $client;
        $this->template = $template;
        $this->updated = $updated;
        $this->old = $old;
        $this->new = $new;
    }
}
