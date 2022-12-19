<?php

declare(strict_types=1);

namespace App\Enums;

enum ContractGateTypeEnum: string
{
    case Location = 'Location';
    case AgreementCategory = 'AgreementCategory';
    case BillingSchedule = 'BillingSchedule';
}
