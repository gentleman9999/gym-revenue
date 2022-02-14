<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\SecurityRoles\SecurityRoleCreated;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleDeleted;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleRestored;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleTrashed;
use App\StorableEvents\Clients\SecurityRoles\SecurityRoleUpdated;

trait ClientSecurityRoleActions
{

    public function createSecurityRole(string $user_id, array $payload)
    {
        $this->recordThat(new SecurityRoleCreated($this->uuid(), $user_id, $payload));
        return $this;
    }

    public function updateSecurityRole(string $user_id, array $payload)
    {
        $this->recordThat(new SecurityRoleUpdated($this->uuid(), $user_id, $payload));
        return $this;
    }

    public function trashSecurityRole(string $user_id, string $id)
    {
        $this->recordThat(new SecurityRoleTrashed($this->uuid(), $user_id, $id));
        return $this;
    }

    public function restoreSecurityRole(string $user_id, string $id)
    {
        $this->recordThat(new SecurityRoleRestored($this->uuid(), $user_id, $id));
        return $this;
    }

    public function deleteSecurityRole(string $user_id, string $id)
    {
        $this->recordThat(new SecurityRoleDeleted($this->uuid(), $user_id, $id));
        return $this;
    }
}
