<?php

namespace App\Domain\SMS\Events;

use App\Models\ClientSmsLog;
use App\StorableEvents\EntityCreated;

class TwilioTracked extends EntityCreated
{
    public function getEntity(): string
    {
        return ClientSmsLog::class;
    }
}
