<?php

namespace App\Aggregates\Clients\Traits\Actions;


use App\StorableEvents\Clients\Activity\Campaigns\LeadSourceCreated;
use App\StorableEvents\Clients\Activity\Campaigns\LeadSourceUpdated;
use App\StorableEvents\Clients\Activity\Leads\LeadStatusCreated;
use App\StorableEvents\Clients\Activity\Leads\LeadStatusUpdated;

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

    public function updateLeadStatus($status, $user_id)
    {
        $this->recordThat(new LeadStatusUpdated($this->uuid(), $status, $user_id));
        return $this;
    }

    public function createLeadStatus($status, $user_id)
    {
        $this->recordThat(new LeadStatusCreated($this->uuid(), $status, $user_id));
        return $this;
    }
}
