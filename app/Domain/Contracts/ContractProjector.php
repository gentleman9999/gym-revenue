<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Contracts\Events\ContractCreated;
use App\Domain\Contracts\Events\ContractDeleted;
use App\Domain\Contracts\Events\ContractRestored;
use App\Domain\Contracts\Events\ContractTrashed;
use App\Domain\Contracts\Events\ContractUpdated;
use App\Domain\Contracts\Projections\Contract;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ContractProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        Contract::delete();
    }

    public function onContractCreated(ContractCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $contracts = (new Contract())->writeable();
            $contracts->fill($event->payload);
            $contracts->id = $event->aggregateRootUuid();
            $contracts->save();
        });
    }

    public function onContractDeleted(ContractDeleted $event): void
    {
        Contract::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onContractRestored(ContractRestored $event): void
    {
        Contract::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onContractTrashed(ContractTrashed $event): void
    {
        Contract::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onContractUpdated(ContractUpdated $event): void
    {
        Contract::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
