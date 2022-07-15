<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\GymRevCrudEvent;

class LeadConverted extends GymRevCrudEvent
{
    public string $member;

    public function __construct(string $member)
    {
        parent::__construct();
        $this->member = $member;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "CONVERTED";
    }
}
