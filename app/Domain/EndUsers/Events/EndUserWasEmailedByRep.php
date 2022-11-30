<?php

namespace App\Domain\EndUsers\Events;

use App\Domain\EndUsers\Projections\EndUser;
use App\StorableEvents\GymRevCrudEvent;

class EndUserWasEmailedByRep extends GymRevCrudEvent
{
    public array $payload;

    public function __construct(array $payload)
    {
        parent::__construct();
        $this->payload = $payload;
    }

    public function getEntity(): string
    {
        return EndUser::class;
    }

    protected function getOperation(): string
    {
        return "EMAILED";
    }
}
