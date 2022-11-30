<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Events;

use App\Domain\Contracts\Projections\Contract;
use App\StorableEvents\EntityUpdated;

class ContractUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Contract::class;
    }
}
