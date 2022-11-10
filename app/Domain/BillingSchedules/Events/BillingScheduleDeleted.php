<?php

namespace App\Domain\BillingSchedules\Events;

use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\StorableEvents\EntityDeleted;

class BillingScheduleDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return BillingSchedule::class;
    }
}
