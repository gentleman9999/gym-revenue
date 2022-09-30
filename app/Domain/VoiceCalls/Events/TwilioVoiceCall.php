<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Events;

use App\StorableEvents\EntityCreated;

class TwilioVoiceCall extends EntityCreated
{
    public function getEntity(): string
    {
        return TwilioVoiceCall::class;
    }
}
