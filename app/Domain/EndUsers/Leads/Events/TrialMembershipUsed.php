<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Leads\Projections\Lead;
use App\StorableEvents\GymRevCrudEvent;

class TrialMembershipUsed extends GymRevCrudEvent
{
    public string $trial;

    public function __construct(string $trial)
    {
        parent::__construct();
        $this->trial = $trial;
    }

    public function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "TRIAL_MEMBERSHIP_USED";
    }
}
