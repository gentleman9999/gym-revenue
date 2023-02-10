<?php

declare(strict_types=1);

namespace App\Domain\Draftable\Events;

use App\StorableEvents\EntityUpdated;
use CapeAndBay\Draftable\Draftable;

class DraftableUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Draftable::class;
    }
}
