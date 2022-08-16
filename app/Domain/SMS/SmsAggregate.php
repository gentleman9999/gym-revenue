<?php

namespace App\Domain\SMS;

use App\Domain\SMS\Events\SmsLog;
use App\Domain\SMS\Events\TwilioTracked;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class SmsAggregate extends AggregateRoot
{
    public function twilioTrack(array $payload): static
    {
        $this->recordThat(new TwilioTracked($payload));

        return $this;
    }

    public function smsLog(array $payload): static
    {
        $this->recordThat(new SmsLog($payload));

        return $this;
    }
}
