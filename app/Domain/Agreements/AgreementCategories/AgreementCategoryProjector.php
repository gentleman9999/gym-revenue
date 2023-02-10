<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories;

use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryCreated;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryDeleted;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryRestored;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryTrashed;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryUpdated;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AgreementCategoryProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        AgreementCategory::all()->delete();
    }

    public function onAgreementCategoryCreated(AgreementCategoryCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $agreement = (new AgreementCategory())->writeable();
            $agreement->fill($event->payload);
            $agreement->id        = $event->aggregateRootUuid();
            $agreement->client_id = $event->payload['client_id'];
            $agreement->save();
        });
    }

    public function onAgreementCategoryDeleted(AgreementCategoryDeleted $event): void
    {
        AgreementCategory::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onAgreementCategoryRestored(AgreementCategoryRestored $event): void
    {
        AgreementCategory::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onAgreementCategoryTrashed(AgreementCategoryTrashed $event): void
    {
        AgreementCategory::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onAgreementCategoryUpdated(AgreementCategoryUpdated $event): void
    {
        AgreementCategory::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
