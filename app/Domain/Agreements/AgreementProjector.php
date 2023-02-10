<?php

declare(strict_types=1);

namespace App\Domain\Agreements;

use App\Domain\Agreements\Events\AgreementCreated;
use App\Domain\Agreements\Events\AgreementDeleted;
use App\Domain\Agreements\Events\AgreementRestored;
use App\Domain\Agreements\Events\AgreementTrashed;
use App\Domain\Agreements\Events\AgreementUpdated;
use App\Domain\Agreements\Projections\Agreement;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AgreementProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        Agreement::truncate();
    }

    public function onAgreementCreated(AgreementCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $agreement = (new Agreement())->writeable();
            $agreement->fill($event->payload);
            $agreement->id        = $event->aggregateRootUuid();
            $agreement->client_id = $event->payload['client_id'];
            $agreement->writeable()->save();
        });
    }

    public function onAgreementDeleted(AgreementDeleted $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onAgreementRestored(AgreementRestored $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onAgreementTrashed(AgreementTrashed $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onAgreementUpdated(AgreementUpdated $event): void
    {
        Agreement::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
