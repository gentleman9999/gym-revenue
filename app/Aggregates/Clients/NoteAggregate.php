<?php

namespace App\Aggregates\Clients;

use App\StorableEvents\Clients\Note\ReadReceiptCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class NoteAggregate extends AggregateRoot
{

    public function createReadReciept(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new ReadReceiptCreated($this->uuid(), $created_by_user_id, $payload));
        return $this;
    }

}
