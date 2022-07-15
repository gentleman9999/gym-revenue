<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\GymRevCrudEvent;

class LeadUpdatedCommunicationPreferences extends GymRevCrudEvent
{
    public bool $email;
    public bool $sms;

    public function __construct(bool $email, bool $sms)
    {
        $this->email = $email;
        $this->sms = $sms;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "COMMUNICATION_PREFERENCES_UPDATED";
    }
}
