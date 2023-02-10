<?php

declare(strict_types=1);

namespace App\Domain\Chat\Events;

use App\Domain\Chat\Models\Chat;
use App\StorableEvents\EntityCreated;

class ChatCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Chat::class;
    }
}
