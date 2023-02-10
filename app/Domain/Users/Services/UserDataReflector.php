<?php

declare(strict_types=1);

namespace App\Domain\Users\Services;

use App\Domain\Users\Models\DataReflections\CustomerReflection as Customer;
use App\Domain\Users\Models\DataReflections\EmployeeReflection as Employee;
use App\Domain\Users\Models\DataReflections\LeadReflection as Lead;
use App\Domain\Users\Models\DataReflections\MemberReflection as Member;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use Illuminate\Support\Facades\Schema;

class UserDataReflector
{
    public static function reflectData(User $user): void
    {
        $cols = Schema::getColumnListing($user->getTable());
        foreach (UserTypesEnum::cases() as $user_type) {
            $reflection_model = self::fetchReflectionModel($user_type, $user->id);

            if ($reflection_model === null && $user->user_type === $user_type) {
                $reflection_model = self::createReflectionModel($user_type);
            }

            if ($reflection_model !== null) {
                foreach ($cols as $col) {
                    if ($col !== 'is_cape_and_bay_user') {
                        if ($col !== 'id') {
                            $reflection_model[$col] = $user[$col];
                        } else {
                            $reflection_model['user_id'] = $user[$col];
                        }
                    }
                }

                $reflection_model->save();
            }
        }
    }

    protected static function fetchReflectionModel(UserTypesEnum $user_type, string $id): Customer|Employee|Lead|Member|null
    {
        switch ($user_type) {
            case UserTypesEnum::CUSTOMER:
                return Customer::withTrashed()->whereUserId($id)->first();
            case UserTypesEnum::EMPLOYEE:
                return Employee::withTrashed()->whereUserId($id)->first();
            case UserTypesEnum::MEMBER:
                return Member::withTrashed()->whereUserId($id)->first();
            default:
                return Lead::withTrashed()->whereUserId($id)->first();
        }
    }

    protected static function createReflectionModel(UserTypesEnum $user_type): Customer|Employee|Lead|Member
    {
        switch ($user_type) {
            case UserTypesEnum::CUSTOMER:
                return new Customer();
            case UserTypesEnum::EMPLOYEE:
                return new Employee();
            case UserTypesEnum::MEMBER:
                return new Member();
            default:
                return new Lead();
        }
    }
}
