<?php

namespace App\StorableEvents\Endusers\Members;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MemberUpdatedCommunicationPreferences extends ShouldBeStored
{
    public $member;
    public $email;
    public $sms;
    public $date;

    public function __construct(string $member, bool $email, bool $sms, string $date)
    {
        $this->member = $member;
        $this->email = $email;
        $this->sms = $sms;
        $this->date = $date;
    }
}
