<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserWasTextMessagedByRep extends GymRevCrudEvent
{
    public array $payload;

    public function __construct(array $payload)
    {
        parent::__construct();
        $this->payload = $payload;
    }

    protected function getOperation(): string
    {
        return "TEXTED";
    }

    public function getEntity(): string
    {
        return EndUser::class;
    }
}
