<?php

declare(strict_types=1);

namespace App\Domain\Users\Events\Notes;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class NoteUpdated extends ShouldBeStored
{
    public string $note_id;
    public array $payload;

    public function __construct(string $note_id, array $payload)
    {
        $this->note_id = $note_id;
        $this->payload = $payload;
    }
}
