<?php

namespace App\StorableEvents;

//WARNING: YOU CANNOT CHANGE THE SHAPE OF STORED EVENTS ONCE THE APP
//         IS IN PROD.  OLD EVENTS WILL BE INCOMPATIBLE AND WILL BREAK
abstract class GymRevCrudEvent extends GymRevShouldBeStored
{
    public function __construct()
    {
        //TODO: we should consider using some alias/enum for entity/class
        //TODO: so the front-end does not have fully qualified classnames when
        //TODO when subscribing to broadcast channels
        $this->metaData['entity'] = $this->getEntity();
        //TODO: use enums for operation key
        $this->metaData['operation'] = $this->getOperation();
    }

    abstract public function getEntity(): string;

    abstract protected function getOperation(): string;
}
