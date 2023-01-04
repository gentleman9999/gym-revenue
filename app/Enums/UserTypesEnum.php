<?php

declare(strict_types=1);

namespace App\Enums;

enum UserTypesEnum: string
{
    case EMPLOYEE = 'employee';
    case MEMBER = 'member';
    case CUSTOMER = 'customer';
    case LEAD = 'lead';

    public static function getByValue($value): ?self
    {
        switch ($value) {
            case self::EMPLOYEE->value:
                return self::EMPLOYEE;
            
            // no break
            case self::MEMBER->value:
                return self::MEMBER;
            case self::CUSTOMER->value:
                return self::CUSTOMER;
            case self::LEAD->value:
                return self::LEAD;
            default:
                return null;
        }
    }
}
