<?php

namespace App\Aggregates\Endusers;

use App\StorableEvents\Endusers\Leads\LeadConverted;
use App\StorableEvents\Endusers\Leads\LeadCreated;
use App\StorableEvents\Endusers\Leads\LeadRestored;
use App\StorableEvents\Endusers\Leads\LeadTrashed;
use App\StorableEvents\Endusers\Leads\LeadUpdated;
use App\StorableEvents\Endusers\Leads\LeadWasCalledByRep;
use App\StorableEvents\Endusers\Leads\LeadWasTextMessagedByRep;
use App\StorableEvents\Endusers\Leads\TrialMembershipUsed;
use App\StorableEvents\Endusers\LeadServicesSet;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LeadAggregate extends AggregateRoot
{
    protected static bool $allowConcurrency = true;
    protected array $lead = [];
    protected int $interaction_count = 0;
    protected int $interaction_called_count = 0;
    protected int $interaction_emailed_count = 0;
    protected int $interaction_text_messaged_count = 0;
    public array $trial_dates = [];

    public function getInteractionCount(): array
    {
        return ['totalCount' => $this->interaction_count,
                'calledCount' => $this->interaction_called_count,
                'smsCount' => $this->interaction_text_messaged_count,
                'emailedCount' => $this->interaction_emailed_count, ];
    }

    public function applyLeadWasCalledByRep(LeadWasCalledByRep $event)
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_called_count++;
    }

    public function applyLeadWasEmailedByRep(\App\StorableEvents\Endusers\Leads\LeadWasEmailedByRep $event)
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_emailed_count++;
    }

    public function applyLeadWasTextMessagedByRep(LeadWasTextMessagedByRep $event)
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_text_messaged_count++;
    }

    public function applyLeadCreated(LeadCreated $event)
    {
        $this->lead = $event->data;
    }

    public function applyLeadUpdated(LeadUpdated $event)
    {
        $this->lead = $event->data;
    }

    public function applyTrialMembershipUsed(TrialMembershipUsed $event)
    {
        $this->trial_dates[] = $event->date;
    }

    public function claim(string $user_id, string $client_id)
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\LeadClaimedByRep($this->uuid(), $user_id, $client_id));

        return $this;
    }

    public function email(array $data, string $user)
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\LeadWasEmailedByRep($this->uuid(), $data, $user));

        return $this;
    }

    public function logPhoneCall(array $data, string $user)
    {
        $this->recordThat(new LeadWasCalledByRep($this->uuid(), $data, $user));

        return $this;
    }

    public function textMessage(array $data, string $user)
    {
        $this->recordThat(new LeadWasTextMessagedByRep($this->uuid(), $data, $user));

        return $this;
    }

    public function addTrialMembership(string $client_id, string $trial_id, $date_started)
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\TrialMembershipAdded($this->uuid(), $client_id, $trial_id, $date_started));
    }

    public function useTrialMembership(string $client_id, string $trial_id, $date_used)
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\TrialMembershipUsed($this->uuid(), $client_id, $trial_id, $date_used));
    }

    public function setServices(array $service_ids, string $user)
    {
        $this->recordThat(new LeadServicesSet($service_ids, $user));

        return $this;
    }

    public function create(array $data, string $userId = 'Auto Generated')
    {
        $this->recordThat(new LeadCreated($userId, $data));

        return $this;
    }

    public function convert(array $data, string $userId = 'Auto Generated')
    {
        $this->recordThat(new LeadConverted($userId, $data));

        return $this;
    }

    public function update(array $data, array $old_data, string $userId = 'Auto Generated')
    {
        $this->recordThat(new LeadUpdated($userId, $data, $old_data));

        return $this;
    }

    public function trash(string $reason, string $userId = 'Auto Generated')
    {
        $this->recordThat(new LeadTrashed($this->uuid(), $userId, $reason));

        return $this;
    }

    public function restore(string $userId = 'Auto Generagted')
    {
        $this->recordThat(new LeadRestored($userId, $this->uuid()));

        return $this;
    }

    public function delete(array $data, string $userId = 'Auto Generated')
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\LeadDeleted($userId, $this->uuid(), $data));

        return $this;
    }

    public function updateCommunicationPreferences(bool $email, bool $sms, string $subscribed_at)
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\LeadUpdatedCommunicationPreferences($this->uuid(), $email, $sms, $subscribed_at));

        return $this;
    }
}
