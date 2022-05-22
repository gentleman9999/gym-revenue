<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Roles\RoleCreated;
use App\StorableEvents\Clients\Roles\RoleDeleted;
use App\StorableEvents\Clients\Roles\RoleRestored;
use App\StorableEvents\Clients\Roles\RoleTrashed;
use App\StorableEvents\Clients\Roles\RoleUpdated;

trait ClientRoleActions
{
    public function createRole(string $user_id, array $payload)
    {
        $this->recordThat(new RoleCreated($this->uuid(), $user_id, $payload));

        return $this;
    }

    public function updateRole(string $user_id, array $payload)
    {
        $this->recordThat(new RoleUpdated($this->uuid(), $user_id, $payload));

        return $this;
    }

    public function trashRole(string $user_id, string $id)
    {
        $this->recordThat(new RoleTrashed($this->uuid(), $user_id, $id));

        return $this;
    }

    public function restoreRole(string $user_id, string $id)
    {
        $this->recordThat(new RoleRestored($this->uuid(), $user_id, $id));

        return $this;
    }

    public function deleteRole(string $user_id, string $id)
    {
        $this->recordThat(new RoleDeleted($this->uuid(), $user_id, $id));

        return $this;
    }
}
