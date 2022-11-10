<?php

namespace App\Domain\BillingSchedules\Events;

use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\StorableEvents\EntityCreated;

class BillingScheduleCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return BillingSchedule::class;
    }
}
