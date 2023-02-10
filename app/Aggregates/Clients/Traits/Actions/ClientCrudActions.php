<?php

declare(strict_types=1);

namespace App\Aggregates\Clients\Traits\Actions;

use App\Domain\Clients\Events\ClientCreated;
use App\Domain\Clients\Events\ClientDeleted;
use App\Domain\Clients\Events\ClientRestored;
use App\Domain\Clients\Events\ClientTrashed;
use App\Domain\Clients\Events\ClientUpdated;

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
