<?php

namespace App\Domain\Email;

use App\Domain\Email\Events\EmailLog;
use App\Domain\Email\Events\MailgunTracked;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EmailAggregate extends AggregateRoot
{
    public function mailgunTrack(array $payload): static
    {
        $this->recordThat(new MailgunTracked($payload));

        return $this;
    }

    public function emailLog(array $payload): static
    {
        $this->recordThat(new EmailLog($payload));

        return $this;
    }
}
