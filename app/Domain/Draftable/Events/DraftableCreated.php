<?php

declare(strict_types=1);

namespace App\Domain\Draftable\Events;

use App\StorableEvents\EntityCreated;
use CapeAndBay\Draftable\Draftable;

class DraftableCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Draftable::class;
    }
}
