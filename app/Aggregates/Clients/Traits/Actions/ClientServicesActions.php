<?php

declare(strict_types=1);

namespace App\Aggregates\Clients\Traits\Actions;

use App\Domain\Clients\Events\ClientServicesSet;

trait ClientServicesActions
{
    public function setClientServices(array $services)
    {
        $this->recordThat(new ClientServicesSet($services));

        return $this;
    }
}
