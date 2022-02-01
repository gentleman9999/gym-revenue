<?php

namespace App\Aggregates\Clients\Traits\Actions;


use App\StorableEvents\Clients\ClientServices\ClientServiceAdded;
use App\StorableEvents\Clients\ClientServices\ClientServiceDisabled;
use App\StorableEvents\Clients\ClientServices\ClientServicesSet;
use App\StorableEvents\Clients\ClientServices\TrialMembershipTypeUpdated;

trait ClientTrialMembershipActions
{
    public function updateTrialMembershipType(array $data, int $user_id)
    {
        $this->recordThat(new TrialMembershipTypeUpdated($this->uuid(), $data, $user_id));
        return $this;
    }
}
