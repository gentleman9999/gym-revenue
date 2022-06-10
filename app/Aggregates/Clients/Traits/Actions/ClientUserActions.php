<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Users\ClientUserStoppedBeingImpersonated;
use App\StorableEvents\Clients\Activity\Users\ClientUserWasImpersonated;
use App\StorableEvents\Clients\Users\UserCreated;
use App\StorableEvents\Clients\Users\UserDeleted;
use App\StorableEvents\Clients\Users\UserUpdated;

trait ClientUserActions
{
    public function createUser(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new UserCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function deleteUser(string $deleted_by_user_id, array $payload)
    {
        $this->recordThat(new UserDeleted($this->uuid(), $deleted_by_user_id, $payload));

        return $this;
    }

    public function updateUser(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new UserUpdated($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function logImpersonationModeActivity(string $employee_user_id, string $impersonating_user_id)
    {
        $this->recordThat(new ClientUserWasImpersonated($this->uuid(), $employee_user_id, $impersonating_user_id, date('Y-m-d H:i:s')));

        return $this;
    }

    public function logImpersonationModeDeactivation(string $employee_user_id, string $impersonating_user_id)
    {
        $this->recordThat(new ClientUserStoppedBeingImpersonated($this->uuid(), $employee_user_id, $impersonating_user_id, date('Y-m-d H:i:s')));

        return $this;
    }
}
