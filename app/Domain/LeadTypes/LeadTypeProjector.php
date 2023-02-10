<?php

declare(strict_types=1);

namespace App\Domain\LeadTypes;

use App\Domain\LeadTypes\Events\LeadTypeCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LeadTypeProjector extends Projector
{
    public function onLeadTypeCreated(LeadTypeCreated $event): void
    {
        $lead_type            = (new LeadType())->writeable();
        $lead_type->id        = $event->aggregateRootUuid();
        $lead_type->client_id = $event->payload['client_id'];
        $lead_type->fill($event->payload);
        $lead_type->save();
    }

//    public function onLeadTypeUpdated(LeadTypeUpdated $event): void
//    {
//        $leadType = LeadType::findOrFail($event->aggregateRootUuid())->writeable();
//        $leadType->updateOrFail($event->payload);
//    }
}
