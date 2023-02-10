<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\Notes;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class NoteUpdated extends ShouldBeStored
{
    public function __construct(public string $client, public string $user, public array $payload)
    {
    }
}
