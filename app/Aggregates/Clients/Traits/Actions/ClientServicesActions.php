<?php

namespace App\Aggregates\Clients\Traits\Actions;


use App\StorableEvents\Clients\ClientServices\ClientServiceAdded;
use App\StorableEvents\Clients\ClientServices\ClientServiceDisabled;

trait ClientServicesActions
{
    public function addClientService(string $slug, string $feature_name, $active = false)
    {
        $this->recordThat(new ClientServiceAdded($this->uuid(), $slug, $feature_name, $active));
        return $this;
    }

    public function enableClientService(string $slug, int $user_id)
    {
        $this->recordThat(new ClientServiceEnabled($this->uuid(), $slug, $user_id));
        return $this;
    }

    public function disableClientService(string $slug, int $user_id)
    {
        $this->recordThat(new ClientServiceDisabled($this->uuid(), $slug, $user_id));
        return $this;
    }
}
