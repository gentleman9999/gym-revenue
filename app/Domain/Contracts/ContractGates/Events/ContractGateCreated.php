<?php

declare(strict_types=1);

namespace App\Domain\Contracts\ContractGates\Events;

use App\Domain\Contracts\ContractGates\Projections\ContractGate;
use App\StorableEvents\EntityCreated;

class ContractGateCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return ContractGate::class;
    }
}
