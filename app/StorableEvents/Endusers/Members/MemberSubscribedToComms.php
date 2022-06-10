<?php

namespace App\StorableEvents\Endusers\Members;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MemberSubscribedToComms extends ShouldBeStored
{
    public $member;
    public $date;

    public function __construct(string $member, string $date)
    {
        $this->member = $member;
        $this->date = $date;
    }
}
