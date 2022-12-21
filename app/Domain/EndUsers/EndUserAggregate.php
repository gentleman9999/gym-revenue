<?php

namespace App\Domain\EndUsers;

use App\Domain\EndUsers\Events\EndUserClaimedByRep;
use App\Domain\EndUsers\Events\EndUserConverted;
use App\Domain\EndUsers\Events\EndUserCreated;
use App\Domain\EndUsers\Events\EndUserDeleted;
use App\Domain\EndUsers\Events\EndUserFileUploaded;
use App\Domain\EndUsers\Events\EndUserProfilePictureMoved;
use App\Domain\EndUsers\Events\EndUserRestored;
use App\Domain\EndUsers\Events\EndUserTrashed;
use App\Domain\EndUsers\Events\EndUserUpdated;
use App\Domain\EndUsers\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Events\EndUserWasCalledByRep;
use App\Domain\EndUsers\Events\EndUserWasEmailedByRep;
use App\Domain\EndUsers\Events\EndUserWasTextMessagedByRep;
use App\Domain\EndUsers\Events\OldEndUserProfilePictureDeleted;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EndUserAggregate extends AggregateRoot
{
//    protected static bool $allowConcurrency = true;
    protected array $endUser = [];
    protected array $oldData = [];
    protected int $interaction_count = 0;
    protected int $interaction_called_count = 0;
    protected int $interaction_emailed_count = 0;
    protected int $interaction_text_messaged_count = 0;

    public function getInteractionCount(): array
    {
        return [
            'totalCount' => $this->interaction_count,
            'calledCount' => $this->interaction_called_count,
            'smsCount' => $this->interaction_text_messaged_count,
            'emailedCount' => $this->interaction_emailed_count,
        ];
    }

    public function applyEndUserWasClaimedToRep(EndUserClaimedByRep $event): void
    {
        $this->claimedByUserId = $event->claimedByUserId;
    }

    public function applyEndUserWasCalledByRep(EndUserWasCalledByRep $event): void
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_called_count++;
    }

    public function applyEndUserWasEmailedByRep(EndUserWasEmailedByRep $event): void
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_emailed_count++;
    }

    public function applyEndUserWasTextMessagedByRep(EndUserWasTextMessagedByRep $event): void
    {
        $this->interaction_count += ($event->data['interaction_count'] ?? 1);
        $this->interaction_text_messaged_count++;
    }

    public function applyEndUserCreated(EndUserCreated $event): void
    {
        $this->endUser = $event->payload;
    }

    public function applyEndUserUpdated(EndUserUpdated $event): void
    {
        $tempOldData = $this->endUser;
        $this->endUser = $event->payload;

        if ($this->endUser !== $this->oldData) {
            $this->oldData = $tempOldData;
        }
    }

    public function getOldData(): array
    {
        return $this->oldData;
    }

    public function create(array $data): static
    {
        $this->recordThat(new EndUserCreated($data));

        return $this;
    }

    public function update(array $data): static
    {
        $this->recordThat(new EndUserUpdated($data));

        return $this;
    }

    public function trash(string $reason): static
    {
        $this->recordThat(new EndUserTrashed($reason));

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new EndUserRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new EndUserDeleted());

        return $this;
    }

    public function claim(string $user_id): static
    {
        $this->recordThat(new EndUserClaimedByRep($user_id));

        return $this;
    }

    public function email(array $data): static
    {
        $this->recordThat(new EndUserWasEmailedByRep($data));

        return $this;
    }

    public function logPhoneCall(array $data): static
    {
        $this->recordThat(new EndUserWasCalledByRep($data));

        return $this;
    }

    public function textMessage(array $data): static
    {
        $this->recordThat(new EndUserWasTextMessagedByRep($data));

        return $this;
    }

    public function updateCommunicationPreferences(bool $email, bool $sms): static
    {
        $this->recordThat(new EndUserUpdatedCommunicationPreferences($email, $sms));

        return $this;
    }

    public function moveProfilePicture(array $file, array $oldFile = null): static
    {
        $this->recordThat(new EndUserProfilePictureMoved($file, $oldFile));

        return $this;
    }

    public function deleteOldProfilePicture(array $oldFile): static
    {
        $this->recordThat(new OldEndUserProfilePictureDeleted($oldFile));

        return $this;
    }

    public function uploadFile(array $payload): static
    {
        $this->recordThat(new EndUserFileUploaded($payload));

        return $this;
    }

    public function convert(string $member_id)
    {
        $this->recordThat(new EndUserConverted($member_id));

        return $this;
    }
}
