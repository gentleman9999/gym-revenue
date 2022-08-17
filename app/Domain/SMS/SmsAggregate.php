<?php

namespace App\Domain\SMS;

use App\Domain\SMS\Events\TwilioTracked;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class SmsAggregate extends AggregateRoot
{
    public function twilioTrack(array $payload): static
    {
        $this->recordThat(new TwilioTracked($payload));

        return $this;
    }
}
