<?php

declare(strict_types=1);

namespace App\Domain\Users\Services;

use App\Domain\Users\Models\DataReflectors\CustomerReflection as Customer;
use App\Domain\Users\Models\DataReflectors\EmployeeReflection as Employee;
use App\Domain\Users\Models\DataReflectors\LeadReflection as Lead;
use App\Domain\Users\Models\DataReflectors\MemberReflection as Member;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use Illuminate\Support\Facades\Schema;

class UserDataReflector
{
    public static function reflectData(User $user): void
    {
        $reflection_models = self::fetchReflectionModels($user);
        $cols = Schema::getColumnListing($user->getTable());

        foreach ($reflection_models as $user_type => $reflection_model) {
            foreach ($cols as $col) {
                $reflection_model[$col] = $user[$col];
            }

            $reflection_model->save();

            if ($user->user_type !== UserTypesEnum::getByValue($user_type)) {
                $reflection_model->delete();
            }
        }
    }

    protected static function fetchReflectionModels(User $user): array
    {
        $reflection_models = [];

        foreach (UserTypesEnum::cases() as $user_type) {
            $reflection_model = self::fetchReflectionModel($user_type, $user->id);

            if (! is_null($reflection_model)) {
                $reflection_models[$user_type->value] = $reflection_model;
            } elseif ($user->user_type === $user_type) {
                $reflection_models[$user_type->value] = self::createReflectionModel($user_type, $user->id);
            }
        }

        return $reflection_models;
    }

    protected static function fetchReflectionModel(UserTypesEnum $user_type, string $id): Customer|Employee|Lead|Member|null
    {
        switch ($user_type) {
            case UserTypesEnum::CUSTOMER:
                return Customer::withTrashed()->find($id);
            case UserTypesEnum::EMPLOYEE:
                return Employee::withTrashed()->find($id);
            case UserTypesEnum::MEMBER:
                return Member::withTrashed()->find($id);
            default:
                return Lead::withTrashed()->find($id);
        }
    }

    protected static function createReflectionModel(UserTypesEnum $user_type, string $id): Customer|Employee|Lead|Member
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
