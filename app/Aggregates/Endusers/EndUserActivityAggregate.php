<?php

namespace App\Aggregates\Endusers;

use App\StorableEvents\Endusers\Leads\LeadCreated;
use App\StorableEvents\Endusers\Leads\LeadRestored;
use App\StorableEvents\Endusers\Leads\LeadTrashed;
use App\StorableEvents\Endusers\Leads\LeadUpdated;
use App\StorableEvents\Endusers\Leads\LeadWasCalledByRep;
use App\StorableEvents\Endusers\Leads\LeadWasTextMessagedByRep;
use App\StorableEvents\Endusers\Leads\TrialMembershipUsed;
use App\StorableEvents\Endusers\LeadServicesSet;
use App\StorableEvents\Endusers\Members\MemberCreated;
use App\StorableEvents\Endusers\Members\MemberDeleted;
use App\StorableEvents\Endusers\Members\MemberRestored;
use App\StorableEvents\Endusers\Members\MemberTrashed;
use App\StorableEvents\Endusers\Members\MemberUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EndUserActivityAggregate extends AggregateRoot
{
    protected static bool $allowConcurrency = true;
    protected array $lead = [];
    protected array $audience_subscriptions = [];
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

    public function joinAudience(string $slug, string $client_id, $entity)
    {
        // @todo - add eval if user is already subscribed and throw an UserActivityException::userAlreadySubscribed Exception
        // @todo - add eval if user belongs to client and throw an UserActivityException::unqualifiedClient Exception
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\SubscribedToAudience($this->uuid(), $slug, $client_id, $entity));

        return $this;
    }

    public function claimLead(string $user_id, string $client_id)
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\LeadClaimedByRep($this->uuid(), $user_id, $client_id));

        return $this;
    }

    public function emailLead(array $data, string $user)
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\LeadWasEmailedByRep($this->uuid(), $data, $user));

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

    public function createLead(array $data, string $userId = 'Auto Generated')
    {
        $this->recordThat(new LeadCreated($userId, $data));

        return $this;
    }

    public function updateLead($data, $old_data, string $userId = 'Auto Generated')
    {
        $this->recordThat(new LeadUpdated($userId, $data, $old_data));

        return $this;
    }

    public function trashLead(string $reason, string $userId = 'Auto Generated')
    {
        $this->recordThat(new LeadTrashed($this->uuid(), $userId, $reason));

        return $this;
    }

    public function restoreLead(string $userId = 'Auto Generagted')
    {
        $this->recordThat(new LeadRestored($userId, $this->uuid()));

        return $this;
    }

    public function deleteLead(array $data, string $userId = 'Auto Generated')
    {
        $this->recordThat(new \App\StorableEvents\Endusers\Leads\LeadDeleted($userId, $this->uuid(), $data));

        return $this;
    }

    public function createMember(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new MemberCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function updateMember(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new MemberUpdated($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function trashMember(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new MemberTrashed($this->uuid(), $trashed_by_user_id, $id));

        return $this;
    }

    public function restoreMember(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new MemberRestored($this->uuid(), $trashed_by_user_id, $id));

        return $this;
    }

    public function deleteMember(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new MemberDeleted($this->uuid(), $trashed_by_user_id, $id));

        return $this;
    }
}
