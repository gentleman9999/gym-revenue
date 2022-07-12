<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\EntityTrashed;

class LeadTrashed extends EntityTrashed
{
    public string $reason;

    public function __construct(string $reason)
    {
        parent::__construct();
        $this->reason = $reason;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }
}
