<?php

declare(strict_types=1);

namespace App\Domain\Draftable\Events;

use App\StorableEvents\EntityDeleted;
use CapeAndBay\Draftable\Draftable;

class DraftableDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Draftable::class;
    }
}
