<?php

namespace App\Domain\EndUsers;

use App\Domain\EndUsers\Events\EndUserCreated;
use App\Domain\EndUsers\Events\EndUserUpdated;
use App\Domain\EndUsers\Events\EndUserWasCalledByRep;
use App\Domain\EndUsers\Events\EndUserWasEmailedByRep;
use App\Domain\EndUsers\Events\EndUserWasTextMessagedByRep;
use App\Domain\EndUsers\Projections\EndUser;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

abstract class EndUserAggregate extends AggregateRoot
{
//    protected static bool $allowConcurrency = true;
    protected array $endUser = [];
    protected array $oldData = [];
    protected int $interaction_count = 0;
    protected int $interaction_called_count = 0;
    protected int $interaction_emailed_count = 0;
    protected int $interaction_text_messaged_count = 0;

    abstract protected static function getModel(): EndUser;

    public function getInteractionCount(): array
    {
        return [
            'totalCount' => $this->interaction_count,
            'calledCount' => $this->interaction_called_count,
            'smsCount' => $this->interaction_text_messaged_count,
            'emailedCount' => $this->interaction_emailed_count,
        ];
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

    abstract public function create(array $data): static;

    abstract public function update(array $data): static;

    abstract public function trash(string $reason): static;

    abstract public function restore(): static;

    abstract public function delete(): static;

    abstract public function claim(string $user_id): static;

    abstract public function email(array $data): static;

    abstract public function logPhoneCall(array $data): static;

    abstract public function textMessage(array $data): static;

    abstract public function updateCommunicationPreferences(bool $email, bool $sms): static;

    abstract public function moveProfilePicture(array $file, array $oldFile = null): static;

    abstract public function deleteOldProfilePicture(array $oldFile): static;
}
