<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Projectors;

use App\Domain\VoiceCalls\Events\ClientVoiceCallLogCreated;
use App\Domain\VoiceCalls\Models\ClientVoiceCallLog;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientVoiceCallProjector extends Projector
{
    public function onClientVoiceCallLogCreated(ClientVoiceCallLogCreated $event): void
    {
        ClientVoiceCallLog::create($event->payload);
    }
}
