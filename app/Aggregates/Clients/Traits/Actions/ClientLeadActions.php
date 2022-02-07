<?php

namespace App\Aggregates\Clients\Traits\Actions;


use App\StorableEvents\Clients\Activity\Campaigns\LeadSourceCreated;
use App\StorableEvents\Clients\Activity\Campaigns\LeadSourceUpdated;

trait ClientLeadActions
{
    public function updateLeadSource($source, $user_id)
    {
        $this->recordThat(new LeadSourceUpdated($this->uuid(), $source, $user_id));
        return $this;
    }

    public function createLeadSource($source, $user_id)
    {
        $this->recordThat(new LeadSourceCreated($this->uuid(), $source, $user_id));
        return $this;
    }
}
