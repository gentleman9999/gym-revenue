<?php

namespace App\Domain\Email\Events;

use App\Domain\Email\Models\MailgunCallback;
use App\StorableEvents\EntityCreated;

class MailgunTracked extends EntityCreated
{
    public function getEntity(): string
    {
        return MailgunCallback::class;
    }
}
