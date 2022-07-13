<?php

namespace App\Domain\Leads\Events;

use App\StorableEvents\GymRevCrudEvent;

class LeadWasTextMessagedByRep extends GymRevCrudEvent
{
    public array $payload;

    public function __construct(array $payload)
    {
        parent::__construct();
        $this->payload = $payload;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "TEXTED";
    }
}
