<?php

declare(strict_types=1);

namespace App\Domain\Contracts\ContractGates;

use App\Domain\Contracts\ContractGates\Events\ContractGateCreated;
use App\Domain\Contracts\ContractGates\Projections\ContractGate;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ContractGateProjector extends Projector
{
    public function onContractGateCreated(ContractGateCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $contractGate = (new ContractGate())->writeable();
            $contractGate->fill($event->payload);
            $contractGate->id = $event->aggregateRootUuid();
            $contractGate->save();
        });
    }
}
