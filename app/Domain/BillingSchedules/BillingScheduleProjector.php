<?php

namespace App\Domain\BillingSchedules;

use App\Domain\BillingSchedules\Events\BillingScheduleCreated;
use App\Domain\BillingSchedules\Events\BillingScheduleDeleted;
use App\Domain\BillingSchedules\Events\BillingScheduleRestored;
use App\Domain\BillingSchedules\Events\BillingScheduleTrashed;
use App\Domain\BillingSchedules\Events\BillingScheduleUpdated;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class BillingScheduleProjector extends Projector
{
    public function onStartingEventReplay()
    {
        BillingSchedule::truncate();
    }

    public function onBillingScheduleCreated(BillingScheduleCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $billing_schedule = (new BillingSchedule())->writeable();
            $billing_schedule->fill($event->payload);
            $billing_schedule->id = $event->aggregateRootUuid();
            $billing_schedule->client_id = $event->payload['client_id'];
            $billing_schedule->save();
        });
    }

    public function onBillingScheduleDeleted(BillingScheduleDeleted $event): void
    {
        BillingSchedule::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onBillingScheduleRestored(BillingScheduleRestored $event): void
    {
        BillingSchedule::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onBillingScheduleTrashed(BillingScheduleTrashed $event): void
    {
        BillingSchedule::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onBillingScheduleUpdated(BillingScheduleUpdated $event): void
    {
        BillingSchedule::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }

    /**
     * @param $endUser
     * @param $type
     * @return void
     */
    public function fill($endUser, $type): void
    {
        $fillable_data = array_filter($endUser->toArray(), function ($key) use ($endUser) {
            return in_array($key, $endUser->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $type->id = $endUser->id;
        $type->client_id = $endUser->client_id;
        $type->email = $endUser->email;
        $type->fill($fillable_data);
        $type->save();
    }
}
