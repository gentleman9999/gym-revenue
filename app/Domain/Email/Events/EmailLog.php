<?php

declare(strict_types=1);

namespace App\Domain\Email\Events;

use App\Models\ClientEmailLog;
use App\StorableEvents\EntityCreated;

class EmailLog extends EntityCreated
{
    public function getEntity(): string
    {
        return ClientEmailLog::class;
    }
}
