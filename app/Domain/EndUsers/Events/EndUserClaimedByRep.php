<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

abstract class EndUserClaimedByRep extends GymRevCrudEvent
{
    public string $claimedByUserId;

    public function __construct(string $claimedByUserId)
    {
        parent::__construct();
        $this->claimedByUserId = $claimedByUserId;
    }

    protected function getOperation(): string
    {
        return "CLAIMED";
    }
}
