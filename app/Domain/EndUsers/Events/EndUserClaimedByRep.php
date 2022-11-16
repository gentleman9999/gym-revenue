<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserClaimedByRep extends GymRevCrudEvent
{
    public function getEntity(): string
    {
        return EndUser::class;
    }

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
