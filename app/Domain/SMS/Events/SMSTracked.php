<?php

namespace App\Domain\SMS\Events;

use App\Models\SmsTracking;
use App\StorableEvents\EntityCreated;

class SMSTracked extends EntityCreated
{
    public function getEntity(): string
    {
        return SmsTracking::class;
    }
}
