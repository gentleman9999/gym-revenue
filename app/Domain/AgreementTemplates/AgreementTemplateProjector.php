<?php

namespace App\Domain\AgreementTemplates;

use App\Domain\AgreementTemplates\Events\AgreementTemplateCreated;
use App\Domain\AgreementTemplates\Events\AgreementTemplateDeleted;
use App\Domain\AgreementTemplates\Events\AgreementTemplateRestored;
use App\Domain\AgreementTemplates\Events\AgreementTemplateTrashed;
use App\Domain\AgreementTemplates\Events\AgreementTemplateUpdated;
use App\Domain\AgreementTemplates\Projections\AgreementTemplate;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AgreementTemplateProjector extends Projector
{
    public function onStartingEventReplay()
    {
        AgreementTemplate::truncate();
    }

    public function onAgreementTemplateCreated(AgreementTemplateCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $agreement = (new AgreementTemplate())->writeable();
            $agreement->fill($event->payload);
            $agreement->id = $event->aggregateRootUuid();
            $agreement->client_id = $event->payload['client_id'];
            $agreement->save();
            if (array_key_exists('billing_schedule', $event->payload)) {
                $agreement->billingSchedule()->sync($event->payload['billing_schedule']);
            }
        });
    }

    public function onAgreementTemplateDeleted(AgreementTemplateDeleted $event): void
    {
        AgreementTemplate::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onAgreementTemplateRestored(AgreementTemplateRestored $event): void
    {
        AgreementTemplate::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onAgreementTemplateTrashed(AgreementTemplateTrashed $event): void
    {
        AgreementTemplate::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onAgreementTemplateUpdated(AgreementTemplateUpdated $event): void
    {
        $agreement = AgreementTemplate::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
        if (array_key_exists('billing_schedule', $event->payload)) {
            $agreement->billingSchedule()->sync($event->payload['billing_schedule']);
        }
    }
}
