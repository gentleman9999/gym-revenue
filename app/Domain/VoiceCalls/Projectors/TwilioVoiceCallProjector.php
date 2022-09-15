<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Projectors;

use App\Domain\VoiceCalls\Events\TwilioVoiceCall;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TwilioVoiceCallProjector extends Projector
{
    public function onTwilioVoiceCall(TwilioVoiceCall $event): void
    {
        //
    }
}
