<?php

namespace App\Aggregates\Clients\Traits\Actions;


use App\StorableEvents\Clients\Activity\Campaigns\LeadSourcesUpdated;

trait ClientLeadActions
{
    public function updateLeadSources($sources, $user_id){
        $this->recordThat(new LeadSourcesUpdated($this->uuid(), $sources, $user_id));
        return $this;
    }
}
