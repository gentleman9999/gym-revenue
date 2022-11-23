<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Events;

use App\Domain\Contracts\Projections\Contract;
use App\StorableEvents\EntityCreated;

class ContractCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Contract::class;
    }
}
