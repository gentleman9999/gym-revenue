<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Events;

use App\Domain\VoiceCalls\Models\ClientVoiceCallLog;
use App\StorableEvents\EntityCreated;

class ClientVoiceCallLogCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return ClientVoiceCallLog::class;
    }
}
