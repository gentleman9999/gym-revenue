<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadUnsubscribedFromComms extends ShouldBeStored
{
    public $lead;
    public $email;
    public $sms;
    public $date;

    public function __construct(string $lead, bool $email, bool $sms, string $date)
    {
        $this->lead = $lead;
        $this->email = $email;
        $this->sms = $sms;
        $this->date = $date;
    }
}
