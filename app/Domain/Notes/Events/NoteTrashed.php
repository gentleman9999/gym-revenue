<?php

declare(strict_types=1);

namespace App\Domain\Notes\Events;

use App\Domain\Notes\Model\Note;
use App\StorableEvents\EntityTrashed;

class NoteTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Note::class;
    }
}
