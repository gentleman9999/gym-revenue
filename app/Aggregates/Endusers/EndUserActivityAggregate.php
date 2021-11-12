<?php

namespace App\Aggregates\Endusers;

use App\Models\User;
use App\StorableEvents\Endusers\NewLeadMade;
use App\StorableEvents\Endusers\UpdateLead;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EndUserActivityAggregate extends AggregateRoot
{
    protected array $lead = [];

    public function applyNewLeadMade(NewLeadMade $event)
    {
        $this->lead = $event->lead;
    }

    public function createNewLead(array $lead)
    {
        $this->recordThat(new NewLeadMade($this->uuid(), $lead));

        return $this;
    }

    public function updateLead(array $lead, User $updating_user)
    {
        $this->recordThat(new UpdateLead($this->uuid(), $lead, $updating_user->id));
        return $this;
    }
}
