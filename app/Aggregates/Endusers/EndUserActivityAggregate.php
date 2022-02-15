<?php

namespace App\Aggregates\Endusers;

use App\Models\User;
use App\StorableEvents\Endusers\AgreementNumberCreatedForLead;
use App\StorableEvents\Endusers\LeadDetailUpdated;
use App\StorableEvents\Endusers\TrialMembershipAdded;
use App\StorableEvents\Endusers\TrialMembershipUsed;
use App\StorableEvents\Endusers\LeadWasCalledByRep;
use App\StorableEvents\Endusers\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\LeadWasTextMessagedByRep;
use App\StorableEvents\Endusers\ManualLeadMade;
use App\StorableEvents\Endusers\NewLeadMade;
use App\StorableEvents\Endusers\SubscribedToAudience;
use App\StorableEvents\Endusers\LeadServicesSet;
use App\StorableEvents\Endusers\UpdateLead;
use App\StorableEvents\Endusers\LeadClaimedByRep;
use App\StorableEvents\Endusers\LeadWasDeleted;
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

        return ['totalCount'    => $this->interaction_count,
                'calledCount'   => $this->interaction_called_count,
                'smsCount'      => $this->interaction_text_messaged_count,
                'emailedCount'  => $this->interaction_emailed_count];
    }

    public function applyLeadWasCalledByRep(LeadWasCalledByRep $event)
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_called_count++;
    }

    public function applyLeadWasEmailedByRep(LeadWasEmailedByRep $event)
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_emailed_count++;
    }

    public function applyLeadWasTextMessagedByRep(LeadWasTextMessagedByRep $event)
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_text_messaged_count++;
    }

    public function applyNewLeadMade(NewLeadMade $event)
    {
        $this->lead = $event->lead;
    }

    public function applyTrialMembershipUsed(TrialMembershipUsed $event){
        $this->trial_dates[] = $event->date;
    }

    public function createNewLead(array $lead)
    {
        $this->recordThat(new NewLeadMade($this->uuid(), $lead));

        return $this;
    }

    public function createAgreementNumberForLead(string $client_id, string $agreement_number)
    {
        $this->recordThat(new AgreementNumberCreatedForLead($this->uuid(),$client_id, $agreement_number));

        return $this;
    }

    public function joinAudience(string $slug, string $client_id, $entity)
    {
        // @todo - add eval if user is already subscribed and throw an UserActivityException::userAlreadySubscribed Exception
        // @todo - add eval if user belongs to client and throw an UserActivityException::unqualifiedClient Exception
        $this->recordThat(new SubscribedToAudience($this->uuid(), $slug, $client_id, $entity));
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

    public function createOrUpdateDetail(string $key, string $value, string $user_id, string $client_id)
    {
        $this->recordThat(new LeadDetailUpdated($this->uuid(), $key, $value, $user_id, $client_id));
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

    public function deleteLead(array $data, string $updating_user)
    {
        $this->recordThat(new LeadWasDeleted($this->uuid(), $data, $updating_user));
        return $this;
    }

    public function addTrialMembership(string $client_id, string $trial_id, $date_started)
    {
        $this->recordThat(new TrialMembershipAdded($this->uuid(), $client_id, $trial_id, $date_started));
    }

    public function useTrialMembership(string $client_id, string $trial_id, $date_used)
    {
        $this->recordThat(new TrialMembershipUsed($this->uuid(),$client_id, $trial_id, $date_used));
    }

    public function setServices(array $service_ids, string $user)
    {
        $this->recordThat(new LeadServicesSet($service_ids, $user));
        return $this;
    }
}
