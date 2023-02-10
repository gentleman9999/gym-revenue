<?php

declare(strict_types=1);

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\TrialMemberships\TrialMembershipTypeCreated;
use App\StorableEvents\Clients\TrialMemberships\TrialMembershipTypeUpdated;

trait ClientTrialMembershipActions
{
    public function createTrialMembershipType(array $data, string $user_id): static
    {
        $this->recordThat(new TrialMembershipTypeCreated($this->uuid(), $data, $user_id));

        return $this;
    }

    public function updateTrialMembershipType(array $data, string $user_id): static
    {
        $this->recordThat(new TrialMembershipTypeUpdated($this->uuid(), $data, $user_id));

        return $this;
    }
}
