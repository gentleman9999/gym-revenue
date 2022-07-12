<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\GymRevCrudEvent;

class LeadClaimedByRep extends GymRevCrudEvent
{
    public string $claimedByUserId;

    public function __construct(string $claimedByUserId)
    {
        parent::__construct();
        $this->claimedByUserId = $claimedByUserId;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "CLAIMED";
    }
}
