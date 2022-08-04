<?php

namespace App\Domain\SMS\Events;

use App\Models\TwilioCallback;
use App\StorableEvents\EntityCreated;

class TwilioTracked extends EntityCreated
{
    public function getEntity(): string
    {
        return TwilioCallback::class;
    }
}
