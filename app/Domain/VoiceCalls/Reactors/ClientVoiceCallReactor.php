<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Reactors;

use App\Domain\VoiceCalls\Events\ClientVoiceCallLogCreated;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientVoiceCallReactor extends Reactor
{
    public function onTwilioVoiceCall(ClientVoiceCallLogCreated $event): void
    {
        // nothing
    }
}
