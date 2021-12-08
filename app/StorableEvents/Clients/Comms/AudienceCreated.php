<?php

namespace App\StorableEvents\Clients\Comms;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AudienceCreated extends ShouldBeStored
{
    public $client, $name, $slug, $email, $from, $user;

    public function __construct($client, $name, $slug, /*$email, $from,*/ $user)
    {
        $this->client = $client;
        $this->name = $name;
        $this->slug = $slug;
        //$this->email = $email;
        //$this->from = $from;
        $this->user = $user;
    }
}
