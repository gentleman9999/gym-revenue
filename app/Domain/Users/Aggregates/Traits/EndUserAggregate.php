<?php

declare(strict_types=1);

namespace App\Domain\Users\Aggregates\Traits;

use App\Domain\Users\Events\EndUserClaimedByRep;
use App\Domain\Users\Events\EndUserConverted;
use App\Domain\Users\Events\EndUserProfilePictureMoved;
use App\Domain\Users\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\Users\Events\EndUserWasCalledByRep;
use App\Domain\Users\Events\EndUserWasEmailedByRep;
use App\Domain\Users\Events\EndUserWasTextMessagedByRep;
use App\Domain\Users\Events\OldEndUserProfilePictureDeleted;

trait EndUserAggregate
{
    protected array $end_user = [];
    protected array $old_data = [];
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
        $this->claimed_by_user_id = $event->claimed_by_user_id;
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

    public function getOldData(): array
    {
        return $this->old_data;
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

    public function moveProfilePicture(array $file, array $old_file = null): static
    {
        $this->recordThat(new EndUserProfilePictureMoved($file, $old_file));

        return $this;
    }

    public function deleteOldProfilePicture(array $old_file): static
    {
        $this->recordThat(new OldEndUserProfilePictureDeleted($old_file));

        return $this;
    }

    public function uploadFile(array $payload): static
    {
        $this->recordThat(new EndUserFileUploaded($payload));

        return $this;
    }

    public function convert(string $member_id): static
    {
        $this->recordThat(new EndUserConverted($member_id));

        return $this;
    }
}
