<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Locations\LocationCreated;
use App\StorableEvents\Clients\Locations\LocationDeleted;
use App\StorableEvents\Clients\Locations\LocationImported;
use App\StorableEvents\Clients\Locations\LocationRestored;
use App\StorableEvents\Clients\Locations\LocationTrashed;
use App\StorableEvents\Clients\Locations\LocationUpdated;

trait ClientLocationsActions
{
    public function createLocation(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new LocationCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function importLocation(string $created_by_user_id, $key)
    {
        $this->recordThat(new LocationImported($this->uuid(), $created_by_user_id, $key));

        return $this;
    }

    public function updateLocation(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new LocationUpdated($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function deleteLocation(string $deleted_by_user_id, array $payload)
    {
        $this->recordThat(new LocationDeleted($this->uuid(), $deleted_by_user_id, $payload));

        return $this;
    }

    public function trashLocation(string $deleted_by_user_id, array $payload)
    {
        $this->recordThat(new LocationTrashed($this->uuid(), $deleted_by_user_id, $payload));

        return $this;
    }

    public function restoreLocation(string $deleted_by_user_id, array $payload)
    {
        $this->recordThat(new LocationRestored($this->uuid(), $deleted_by_user_id, $payload));

        return $this;
    }
}
