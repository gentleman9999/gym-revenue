<?php

namespace App\Domain\EndUsers\Leads;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\Events\LeadClaimedByRep;
use App\Domain\EndUsers\Leads\Events\LeadConverted;
use App\Domain\EndUsers\Leads\Events\LeadCreated;
use App\Domain\EndUsers\Leads\Events\LeadDeleted;
use App\Domain\EndUsers\Leads\Events\LeadProfilePictureMoved;
use App\Domain\EndUsers\Leads\Events\LeadRestored;
use App\Domain\EndUsers\Leads\Events\LeadTrashed;
use App\Domain\EndUsers\Leads\Events\LeadUpdated;
use App\Domain\EndUsers\Leads\Events\LeadUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Leads\Events\LeadWasCalledByRep;
use App\Domain\EndUsers\Leads\Events\LeadWasEmailedByRep;
use App\Domain\EndUsers\Leads\Events\LeadWasTextMessagedByRep;
use App\Domain\EndUsers\Leads\Events\OldLeadProfilePictureDeleted;
use App\Domain\EndUsers\Leads\Events\TrialMembershipAdded;
use App\Domain\EndUsers\Leads\Events\TrialMembershipUsed;
use App\Domain\EndUsers\Projections\EndUser;

class LeadAggregate extends EndUserAggregate
{
    public array $trial_dates = [];

    protected static function getModel(): EndUser
    {
        return new Lead();
    }

    public function create(array $data): static
    {
        $this->recordThat(new LeadCreated($data));

        return $this;
    }

    public function update(array $data): static
    {
        $this->recordThat(new LeadUpdated($data));

        return $this;
    }

    public function trash(string $reason): static
    {
        $this->recordThat(new LeadTrashed($reason));

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new LeadRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new LeadDeleted());

        return $this;
    }

    public function claim(string $user_id): static
    {
        $this->recordThat(new LeadClaimedByRep($user_id));

        return $this;
    }

    public function email(array $data): static
    {
        $this->recordThat(new LeadWasEmailedByRep($data));

        return $this;
    }

    public function logPhoneCall(array $data): static
    {
        $this->recordThat(new LeadWasCalledByRep($data));

        return $this;
    }

    public function textMessage(array $data): static
    {
        $this->recordThat(new LeadWasTextMessagedByRep($data));

        return $this;
    }

    public function updateCommunicationPreferences(bool $email, bool $sms): static
    {
        $this->recordThat(new LeadUpdatedCommunicationPreferences($email, $sms));

        return $this;
    }

    public function moveProfilePicture(array $file, array $oldFile = null): static
    {
        $this->recordThat(new LeadProfilePictureMoved($file, $oldFile));

        return $this;
    }

    public function deleteOldProfilePicture(array $oldFile): static
    {
        $this->recordThat(new OldLeadProfilePictureDeleted($oldFile));

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
}
