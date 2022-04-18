<?php

namespace App\Aggregates\Clients;


use App\StorableEvents\Clients\Note\ShortUrlCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ShortUrlAggregate extends AggregateRoot
{

    public function createShortUrl(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new ShortUrlCreated($this->uuid(), $created_by_user_id, $payload));
        return $this;
    }

}
