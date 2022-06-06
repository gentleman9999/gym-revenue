<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

//WARNING: YOU CANNOT CHANGE THE SHAPE OF STORED EVENTS ONCE THE APP
//         IS IN PROD.  OLD EVENTS WILL BE INCOMPATIBLE AND WILL BREAK
abstract class EntityUpdated extends ShouldBeStored
{
    //the data/payload associated with the update event
    public $payload;
    //the user who initiated the event. null = system generated
    public $user;

    public function __construct(array $payload, string | null $user = null)
    {
        $this->payload = $payload;
        $this->user = $user;
    }
}
