<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\Domain\Clients\Events\ClientCommsPrefsSet;

trait ClientCommsPrefsActions
{
    public function setCommsPrefs(array $commPreferences)
    {
        $this->recordThat(new ClientCommsPrefsSet($commPreferences));

        return $this;
    }
}
