<?php

namespace App\Aggregates\Endusers;

use App\Models\User;
use App\StorableEvents\Endusers\LeadWasCalledByRep;
use App\StorableEvents\Endusers\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\LeadWasTextMessagedByRep;
use App\StorableEvents\Endusers\ManualLeadMade;
use App\StorableEvents\Endusers\NewLeadMade;
use App\StorableEvents\Endusers\UpdateLead;
use App\StorableEvents\Endusers\LeadClaimedByRep;
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

    public function manualNewLead(array $lead, string $user_id)
    {
        $this->recordThat(new ManualLeadMade($this->uuid(), $lead, $user_id));

        return $this;
    }

    public function updateLead(array $lead, User $updating_user)
    {
        $this->recordThat(new UpdateLead($this->uuid(), $lead, $updating_user->id));
        return $this;
    }

    public function claimLead(string $user_id, string $client_id)
    {
        $this->recordThat(new LeadClaimedByRep($this->uuid(), $user_id, $client_id));
        return $this;
    }

    public function emailLead(array $data, string $user)
    {
        $this->recordThat(new LeadWasEmailedByRep($this->uuid(), $data, $user));
        return $this;
    }

    public function logPhoneCallWithLead(array $data, string $user)
    {
        $this->recordThat(new LeadWasCalledByRep($this->uuid(), $data, $user));
        return $this;
    }

    public function textMessageLead(array $data, string $user)
    {
        $this->recordThat(new LeadWasTextMessagedByRep($this->uuid(), $data, $user));
        return $this;
    }
}
