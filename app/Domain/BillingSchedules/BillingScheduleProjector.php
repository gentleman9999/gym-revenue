<?php

declare(strict_types=1);

namespace App\Domain\BillingSchedules;

use App\Domain\BillingSchedules\Events\BillingScheduleCreated;
use App\Domain\BillingSchedules\Events\BillingScheduleDeleted;
use App\Domain\BillingSchedules\Events\BillingScheduleRestored;
use App\Domain\BillingSchedules\Events\BillingScheduleTrashed;
use App\Domain\BillingSchedules\Events\BillingScheduleUpdated;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Domain\Users\Models\EndUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class BillingScheduleProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        BillingSchedule::truncate();
    }

    public function onBillingScheduleCreated(BillingScheduleCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $billing_schedule = (new BillingSchedule())->writeable();
            $billing_schedule->fill($event->payload);
            $billing_schedule->id        = $event->aggregateRootUuid();
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
     * @param $end_user
     * @param $type
     */
    public function fill(EndUser $end_user, Model $type): void
    {
        $fillable_data   = array_filter($end_user->toArray(), function ($key) use ($end_user) {
            return in_array($key, $end_user->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $type->id        = $end_user->id;
        $type->client_id = $end_user->client_id;
        $type->email     = $end_user->email;
        $type->fill($fillable_data);
        $type->save();
    }
}
