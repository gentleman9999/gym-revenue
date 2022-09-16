<?php

namespace App\Domain\DynamicReports\Events;

use App\Models\DynamicReport;
use App\StorableEvents\EntityCreated;

class DynamicReportCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return DynamicReport::class;
    }
}
