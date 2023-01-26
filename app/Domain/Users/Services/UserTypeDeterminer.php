<?php

declare(strict_types=1);

namespace App\Domain\Users\Services;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Agreements\Projections\Agreement;
use App\Domain\LocationEmployees\Projections\LocationEmployee;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;

class UserTypeDeterminer
{
    public static function getUserType(User $user): UserTypesEnum
    {
        if (! is_null(LocationEmployee::whereUserId($user->id)->first())) {
            return UserTypesEnum::EMPLOYEE;
        }

        $agreements = Agreement::with('categoryById')->whereActive(1)->whereUserId($user->id)->get();

        if (count($agreements) === 0) {
            return UserTypesEnum::LEAD;
        }

        foreach ($agreements as $agreement) {
            if ($agreement->categoryById && $agreement->categoryById['name'] === AgreementCategory::NAME_MEMBERSHIP) {
                return UserTypesEnum::MEMBER;
            }
        }

        return UserTypesEnum::CUSTOMER;
    }
}
