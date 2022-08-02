<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\Domain\Locations\Events\LocationsImported;
use App\Domain\Users\Events\UsersImported;

trait ClientImportActions
{
    public function importUsers(string $key, string $client_id): static
    {
        $this->recordThat(new UsersImported($key, $client_id));

        return $this;
    }

    public function importLocations(string $key, string $client_id): static
    {
        $this->recordThat(new LocationsImported($key, $client_id));

        return $this;
    }
}
