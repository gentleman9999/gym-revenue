<?php

namespace App\Aggregates\Clients\Traits\Actions;


use App\StorableEvents\Clients\ClientServices\ClientServiceAdded;
use App\StorableEvents\Clients\ClientServices\ClientServiceDisabled;
use App\StorableEvents\Clients\ClientServices\ClientServicesSet;

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

    public function setClientServices(array $service_slugs, $user_id)
    {
        $this->recordThat(new ClientServicesSet($this->uuid(), $service_slugs, $user_id));
        return $this;
    }
}
