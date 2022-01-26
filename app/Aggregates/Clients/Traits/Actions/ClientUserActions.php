<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\UserCreated;
use App\StorableEvents\Clients\UserDeleted;
use App\StorableEvents\Clients\UserUpdated;

trait ClientUserActions
{

    public function createUser(string $user_id, array $payload)
    {
        $this->recordThat(new UserCreated($this->uuid(), $user_id, $payload));
        return $this;
    }

    public function deleteUser(string $user_id, array $payload)
    {
        $this->recordThat(new UserDeleted($this->uuid(), $user_id, $payload));
        return $this;
    }

    public function updateUser(string $user_id, array $payload)
    {
        $this->recordThat(new UserUpdated($this->uuid(), $user_id, $payload));
        return $this;
    }
}
