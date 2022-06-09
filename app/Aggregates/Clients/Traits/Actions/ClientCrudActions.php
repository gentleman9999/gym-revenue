<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\ClientCreated;
use App\StorableEvents\Clients\ClientDeleted;
use App\StorableEvents\Clients\ClientRestored;
use App\StorableEvents\Clients\ClientTrashed;
use App\StorableEvents\Clients\ClientUpdated;

trait ClientCrudActions
{
    public function create(array $payload)
    {
        $this->recordThat(new ClientCreated($payload));

        return $this;
    }

    public function update(array $payload)
    {
        $this->recordThat(new ClientUpdated($payload));

        return $this;
    }

    public function trash()
    {
        $this->recordThat(new ClientTrashed());

        return $this;
    }

    public function restore()
    {
        $this->recordThat(new ClientRestored());

        return $this;
    }

    public function delete()
    {
        $this->recordThat(new ClientDeleted());

        return $this;
    }
}
