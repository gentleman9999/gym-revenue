<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

//WARNING: YOU CANNOT CHANGE THE SHAPE OF STORED EVENTS ONCE THE APP
//         IS IN PROD.  OLD EVENTS WILL BE INCOMPATIBLE AND WILL BREAK
abstract class EntityTrashed extends ShouldBeStored
{
    //the user who initiated the event. null = system generated
    public $user;

    public function __construct(string | null $user = null)
    {
        $this->user = $user;
    }
}
