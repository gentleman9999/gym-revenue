<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\Models\User;
use App\StorableEvents\Clients\ClientCreated;
use App\StorableEvents\Clients\ClientDeleted;
use App\StorableEvents\Clients\ClientRestored;
use App\StorableEvents\Clients\ClientTrashed;
use App\StorableEvents\Clients\ClientUpdated;

trait ClientCrudActions
{
    public function create(array $payload, User | string | null $created_by_user = null)
    {
        $this->recordThat(new ClientCreated($payload, $created_by_user->id ?? null));

        return $this;
    }

    public function trash(User | string | null  $trashed_by_user = null)
    {
        $this->recordThat(new ClientTrashed($trashed_by_user->id ?? null));

        return $this;
    }

    public function restore(User | string | null  $restored_by_user = null)
    {
        $this->recordThat(new ClientRestored($restored_by_user->id ?? null));

        return $this;
    }

    public function delete(User | string | null  $deleted_by_user = null)
    {
        $deleted_by_user_id = $deleted_by_user->id ?? null;

        $this->recordThat(new ClientDeleted($deleted_by_user_id));

        return $this;
    }

    public function update(array $payload, User | string | null  $updated_by_user = null)
    {
        $this->recordThat(new ClientUpdated($payload, $updated_by_user->id ?? null));

        return $this;
    }
}
