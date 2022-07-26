<?php

namespace App\Domain\EndUsers\Members;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\Events\MemberClaimedByRep;
use App\Domain\EndUsers\Members\Events\MemberCreated;
use App\Domain\EndUsers\Members\Events\MemberDeleted;
use App\Domain\EndUsers\Members\Events\MemberProfilePictureMoved;
use App\Domain\EndUsers\Members\Events\MemberRestored;
use App\Domain\EndUsers\Members\Events\MemberTrashed;
use App\Domain\EndUsers\Members\Events\MemberUpdated;
use App\Domain\EndUsers\Members\Events\MemberUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Members\Events\MemberWasCalledByRep;
use App\Domain\EndUsers\Members\Events\MemberWasEmailedByRep;
use App\Domain\EndUsers\Members\Events\MemberWasTextMessagedByRep;
use App\Domain\EndUsers\Members\Events\OldMemberProfilePictureDeleted;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;

class MemberAggregate extends EndUserAggregate
{
    protected static function getModel(): EndUser
    {
        return new Member();
    }

    public function create(array $data): static
    {
        $this->recordThat(new MemberCreated($data));

        return $this;
    }

    public function update(array $data): static
    {
        $this->recordThat(new MemberUpdated($data));

        return $this;
    }

    public function trash(string $reason): static
    {
        $this->recordThat(new MemberTrashed($reason));

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new MemberRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new MemberDeleted());

        return $this;
    }

    public function claim(string $user_id): static
    {
        $this->recordThat(new MemberClaimedByRep($user_id));

        return $this;
    }

    public function email(array $data): static
    {
        $this->recordThat(new MemberWasEmailedByRep($data));

        return $this;
    }

    public function logPhoneCall(array $data): static
    {
        $this->recordThat(new MemberWasCalledByRep($data));

        return $this;
    }

    public function textMessage(array $data): static
    {
        $this->recordThat(new MemberWasTextMessagedByRep($data));

        return $this;
    }

    public function updateCommunicationPreferences(bool $email, bool $sms): static
    {
        $this->recordThat(new MemberUpdatedCommunicationPreferences($email, $sms));

        return $this;
    }

    public function moveProfilePicture(array $file, array $oldFile = null): static
    {
        $this->recordThat(new MemberProfilePictureMoved($file, $oldFile));

        return $this;
    }

    public function deleteOldProfilePicture(array $oldFile): static
    {
        $this->recordThat(new OldMemberProfilePictureDeleted($oldFile));

        return $this;
    }
}
