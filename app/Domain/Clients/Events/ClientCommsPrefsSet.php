<?php

declare(strict_types=1);

namespace App\Domain\Clients\Events;

use App\Domain\Clients\Projections\Client;
use App\StorableEvents\GymRevCrudEvent;

class ClientCommsPrefsSet extends GymRevCrudEvent
{
    public array $commsPreferences;

    public function __construct(array $commsPreferences)
    {
        $this->commsPreferences = $commsPreferences;
        parent::__construct();
    }

    public function getEntity(): string
    {
        return Client::class;
    }

    protected function getOperation(): string
    {
        return "PREFERENCES_SET";
    }
}
