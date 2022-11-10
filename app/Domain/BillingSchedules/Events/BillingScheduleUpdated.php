<?php

namespace App\Domain\BillingSchedules\Events;

use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\StorableEvents\EntityUpdated;

class BillingScheduleUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return BillingSchedule::class;
    }
}
