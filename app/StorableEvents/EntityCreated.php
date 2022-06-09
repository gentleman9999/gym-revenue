<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

//WARNING: YOU CANNOT CHANGE THE SHAPE OF STORED EVENTS ONCE THE APP
//         IS IN PROD.  OLD EVENTS WILL BE INCOMPATIBLE AND WILL BREAK
abstract class EntityCreated extends ShouldBeStored
{
    //the data/payload associated with the creation event
    public $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
