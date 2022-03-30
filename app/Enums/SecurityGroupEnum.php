<?php
namespace App\Enums;

enum SecurityGroupEnum: int
{
    case ADMIN = 1;
    case ACCOUNT_OWNER = 2;
    case REGIONAL_ADMIN = 3;
    case LOCATION_MANAGER = 4;
    case SALES_REP = 5;
    case EMPLOYEE = 6;
}
