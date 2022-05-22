<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\ClientServices\TrialMembershipTypeCreated;
use App\StorableEvents\Clients\ClientServices\TrialMembershipTypeUpdated;

trait ClientTrialMembershipActions
{
    public function createTrialMembershipType(array $data, int $user_id)
    {
        $this->recordThat(new TrialMembershipTypeCreated($this->uuid(), $data, $user_id));

        return $this;
    }

    public function updateTrialMembershipType(array $data, int $user_id)
    {
        $this->recordThat(new TrialMembershipTypeUpdated($this->uuid(), $data, $user_id));

        return $this;
    }
}
