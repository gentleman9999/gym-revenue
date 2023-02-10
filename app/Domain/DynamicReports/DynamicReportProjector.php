<?php

declare(strict_types=1);

namespace App\Domain\DynamicReports;

use App\Domain\DynamicReports\Events\DynamicReportCreated;
use App\Domain\DynamicReports\Events\DynamicReportDeleted;
use App\Domain\DynamicReports\Events\DynamicReportRestored;
use App\Domain\DynamicReports\Events\DynamicReportTrashed;
use App\Domain\DynamicReports\Events\DynamicReportUpdated;
use App\Models\DynamicReport;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DynamicReportProjector extends Projector
{
    public function onDynamicReportCreated(DynamicReportCreated $event): void
    {
        $dr = (new DynamicReport());
        $dr->fill($event->payload);
        $dr->id        = $event->aggregateRootUuid();
        $dr->client_id = $event->clientId();
        $dr->writeable()->save();
    }

    public function onDynamicReportUpdated(DynamicReportUpdated $event): void
    {
        $dr = DynamicReport::findOrFail($event->aggregateRootUuid());

        $dr->writeable()->updateOrFail($event->payload);
    }

    public function onDynamicReportTrashed(DynamicReportTrashed $event): void
    {
        DynamicReport::withTrashed()->findOrFail($event->aggregateRootUuid())->delete();
    }

    public function onDynamicReportRestored(DynamicReportRestored $event): void
    {
        DynamicReport::withTrashed()->findOrFail($event->aggregateRootUuid())->restore();
    }

    public function onDynamicReportDeleted(DynamicReportDeleted $event): void
    {
        DynamicReport::withTrashed()->findOrFail($event->aggregateRootUuid())->forceDelete();
    }
}
