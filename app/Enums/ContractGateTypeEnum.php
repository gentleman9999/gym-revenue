<?php

declare(strict_types=1);

namespace App\Enums;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Domain\Locations\Projections\Location;

enum ContractGateTypeEnum: string
{
    case Location          = Location::class;
    case AgreementCategory = AgreementCategory::class;
    case BillingSchedule   = BillingSchedule::class;
}
