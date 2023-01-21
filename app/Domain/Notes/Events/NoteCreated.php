<?php

declare(strict_types=1);

namespace App\Domain\Notes\Events;

use App\Domain\Notes\Model\Note;
use App\StorableEvents\EntityUpdated;

class NoteCreated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Note::class;
    }
}
