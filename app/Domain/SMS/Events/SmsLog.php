<?php

declare(strict_types=1);

namespace App\Domain\SMS\Events;

use App\Models\ClientSmsLog;
use App\StorableEvents\EntityCreated;

class SmsLog extends EntityCreated
{
    public function getEntity(): string
    {
        return ClientSmsLog::class;
    }
}
