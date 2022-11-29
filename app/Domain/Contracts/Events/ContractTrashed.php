<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Events;

use App\Domain\Contracts\Projections\Contract;
use App\StorableEvents\EntityTrashed;

class ContractTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Contract::class;
    }
}
