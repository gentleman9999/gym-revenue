<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

abstract class EndUserUpdatedCommunicationPreferences extends GymRevCrudEvent
{
    public bool $email;
    public bool $sms;

    public function __construct(bool $email, bool $sms)
    {
        parent::__construct();
        $this->email = $email;
        $this->sms = $sms;
    }

    protected function getOperation(): string
    {
        return "COMMUNICATION_PREFERENCES_UPDATED";
    }
}
