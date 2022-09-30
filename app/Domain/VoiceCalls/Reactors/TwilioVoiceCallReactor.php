<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Reactors;

use App\Domain\VoiceCalls\Events\TwilioVoiceCall;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class TwilioVoiceCallReactor extends Reactor
{
    public function onTwilioVoiceCall(TwilioVoiceCall $event): void
    {
        // nothing
    }
}
