<?php

namespace App\Domain\BillingSchedules\Events;

use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\StorableEvents\EntityTrashed;

class BillingScheduleTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return BillingSchedule::class;
    }
}
