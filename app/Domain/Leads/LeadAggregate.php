<?php

namespace App\Domain\Leads;

use App\Domain\Leads\Events\LeadClaimedByRep;
use App\Domain\Leads\Events\LeadConverted;
use App\Domain\Leads\Events\LeadCreated;
use App\Domain\Leads\Events\LeadDeleted;
use App\Domain\Leads\Events\LeadRestored;
use App\Domain\Leads\Events\LeadTrashed;
use App\Domain\Leads\Events\LeadUpdated;
use App\Domain\Leads\Events\LeadUpdatedCommunicationPreferences;
use App\Domain\Leads\Events\LeadWasCalledByRep;
use App\Domain\Leads\Events\LeadWasEmailedByRep;
use App\Domain\Leads\Events\LeadWasTextMessagedByRep;
use App\Domain\Leads\Events\TrialMembershipAdded;
use App\Domain\Leads\Events\TrialMembershipUsed;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LeadAggregate extends AggregateRoot
{
//    protected static bool $allowConcurrency = true;
    protected array $lead = [];
    protected array $oldData = [];
    protected int $interaction_count = 0;
    protected int $interaction_called_count = 0;
    protected int $interaction_emailed_count = 0;
    protected int $interaction_text_messaged_count = 0;
    public array $trial_dates = [];

    public function getInteractionCount(): array
    {
        return [
            'totalCount' => $this->interaction_count,
            'calledCount' => $this->interaction_called_count,
            'smsCount' => $this->interaction_text_messaged_count,
            'emailedCount' => $this->interaction_emailed_count,
        ];
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

    public function applyLeadCreated(LeadCreated $event)
    {
        $this->lead = $event->payload;
    }

    public function applyLeadUpdated(LeadUpdated $event)
    {
        $tempOldData = $this->lead;
        $this->lead = $event->payload;

        if ($this->lead !== $this->oldData) {
            $this->oldData = $tempOldData;
        }
    }

    public function applyTrialMembershipUsed(TrialMembershipUsed $event)
    {
        $this->trial_dates[] = $event->createdAt();
    }

    public function getOldData(): array
    {
        return $this->oldData;
    }

    public function create(array $data)
    {
        $this->recordThat(new LeadCreated($data));

        return $this;
    }

    public function update(array $data)
    {
        $this->recordThat(new LeadUpdated($data));

        return $this;
    }

    public function trash(string $reason)
    {
        $this->recordThat(new LeadTrashed($reason));

        return $this;
    }

    public function restore()
    {
        $this->recordThat(new LeadRestored());

        return $this;
    }

    public function delete()
    {
        $this->recordThat(new LeadDeleted());

        return $this;
    }

    public function claim(string $user_id)
    {
        $this->recordThat(new LeadClaimedByRep($user_id));

        return $this;
    }

    public function email(array $data)
    {
        $this->recordThat(new LeadWasEmailedByRep($data));

        return $this;
    }

    public function logPhoneCall(array $data)
    {
        $this->recordThat(new LeadWasCalledByRep($data));

        return $this;
    }

    public function textMessage(array $data)
    {
        $this->recordThat(new LeadWasTextMessagedByRep($data));

        return $this;
    }

    public function addTrialMembership(string $trial_id)
    {
        $this->recordThat(new TrialMembershipAdded($trial_id));
    }

    public function useTrialMembership(string $trial_id)
    {
        $this->recordThat(new TrialMembershipUsed($trial_id));
    }

    public function convert(string $memberId)
    {
        $this->recordThat(new LeadConverted($memberId));

        return $this;
    }

    public function updateCommunicationPreferences(bool $email, bool $sms)
    {
        $this->recordThat(new LeadUpdatedCommunicationPreferences($email, $sms));

        return $this;
    }
}
