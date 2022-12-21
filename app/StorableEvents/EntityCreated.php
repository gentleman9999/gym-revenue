<?php

namespace App\StorableEvents;

//WARNING: YOU CANNOT CHANGE THE SHAPE OF STORED EVENTS ONCE THE APP
//         IS IN PROD.  OLD EVENTS WILL BE INCOMPATIBLE AND WILL BREAK
abstract class EntityCreated extends GymRevCrudEvent
{
    //the data/payload associated with the creation event
    public array $payload;

    public function __construct(array $payload)
    {
        parent::__construct();
        $this->payload = $payload;
    }

    protected function getOperation(): string
    {
        return "CREATED";
    }
}
