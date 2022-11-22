<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Events;

use App\Domain\Contracts\Projections\Contract;
use App\StorableEvents\EntityDeleted;

class ContractDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return Contract::class;
    }
}
