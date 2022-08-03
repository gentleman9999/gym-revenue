<?php

namespace App\Domain\SMS;

use App\Domain\SMS\Events\SMSTracked;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class SmsAggregate extends AggregateRoot
{
    public function track(array $payload): static
    {
        $this->recordThat(new SMSTracked($payload));

        return $this;
    }
}
