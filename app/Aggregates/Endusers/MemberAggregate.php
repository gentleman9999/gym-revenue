<?php

namespace App\Aggregates\Endusers;

use App\StorableEvents\Endusers\Members\MemberCreated;
use App\StorableEvents\Endusers\Members\MemberDeleted;
use App\StorableEvents\Endusers\Members\MemberRestored;
use App\StorableEvents\Endusers\Members\MemberTrashed;
use App\StorableEvents\Endusers\Members\MemberUpdated;
use App\StorableEvents\Endusers\Members\MemberUpdatedCommunicationPreferences;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class MemberAggregate extends AggregateRoot
{
    public function create(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new MemberCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function update(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new MemberUpdated($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function trash(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new MemberTrashed($this->uuid(), $trashed_by_user_id, $id));

        return $this;
    }

    public function restore(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new MemberRestored($this->uuid(), $trashed_by_user_id, $id));

        return $this;
    }

    public function delete(string $trashed_by_user_id, string $id)
    {
        $this->recordThat(new MemberDeleted($this->uuid(), $trashed_by_user_id, $id));

        return $this;
    }

    public function updateCommunicationPreferences(bool $email, bool $sms, string $subscribed_at)
    {
        $this->recordThat(new MemberUpdatedCommunicationPreferences($this->uuid(), $email, $sms, $subscribed_at));

        return $this;
    }
}
