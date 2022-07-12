<?php

namespace App\Domain\Leads\Events;

use App\StorableEvents\GymRevCrudEvent;

class TrialMembershipUsed extends GymRevCrudEvent
{
    public string $trial;

    public function __construct(string $trial)
    {
        parent::__construct();
        $this->trial = $trial;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "TRIAL_MEMBERSHIP_USED";
    }
}
