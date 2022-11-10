<?php

namespace App\Domain\BillingSchedules\Events;

use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\StorableEvents\EntityRestored;

class BillingScheduleRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return BillingSchedule::class;
    }
}
