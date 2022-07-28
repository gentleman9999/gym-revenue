<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Leads\Projections\Lead;
use App\StorableEvents\GymRevCrudEvent;

class LeadConverted extends GymRevCrudEvent
{
    public string $member;

    public function __construct(string $member)
    {
        parent::__construct();
        $this->member = $member;
    }

    public function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "CONVERTED";
    }
}
