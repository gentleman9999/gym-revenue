<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\DataReflectors\CustomerReflection as Customer;
use App\Domain\Users\Models\DataReflectors\EmployeeReflection as Employee;
use App\Domain\Users\Models\DataReflectors\LeadReflection as Lead;
use App\Domain\Users\Models\DataReflectors\MemberReflection as Member;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\Concerns\AsAction;

//TODO: WTF  is this? ALL DATA MUTATIONS NEED TO BE IN PROJECTORS, AND ONLY IN PROJECTORS
class ReflectUserData
{
    use AsAction;

    public function handle(User $user, ?UserTypesEnum $previous_type = null): void
    {
        $this->deleteRedundantReflection($user, $previous_type);
        $reflection_model = $this->getReflectionModel($user->user_type, $user->id);
        $cols = Schema::getColumnListing($user->getTable());

        foreach ($cols as $col) {
            if ($col != 'created_at' && $col != 'updated_at') {
                $reflection_model[$col] = $user[$col];
            }
        }

        $reflection_model->save();
    }

    protected function getReflectionModel(UserTypesEnum $user_type, string $id): Customer|Employee|Lead|Member
    {
        $reflection_model = $this->fetchReflectionModel($user_type, $id);

        if ($reflection_model == null) {
            $reflection_model = $this->createReflectionModel($user_type, $id);
        }

        return $reflection_model;
    }

    protected function fetchReflectionModel(UserTypesEnum $user_type, string $id): Customer|Employee|Lead|Member|null
    {
        switch ($user_type) {
            case UserTypesEnum::CUSTOMER:
                return Customer::find($id);
            case UserTypesEnum::EMPLOYEE:
                return Employee::find($id);
            case UserTypesEnum::MEMBER:
                return Member::find($id);
            default:
                return Lead::find($id);
        }
    }

    protected function createReflectionModel(UserTypesEnum $user_type, string $id): Customer|Employee|Lead|Member
    {
        switch ($user_type) {
            case UserTypesEnum::CUSTOMER:
                return Customer::withTrashed()->find($id) ?? new Customer();
            case UserTypesEnum::EMPLOYEE:
                return Employee::withTrashed()->find($id) ?? new Employee();
            case UserTypesEnum::MEMBER:
                return Member::withTrashed()->find($id) ?? new Member();
            default:
                return Lead::withTrashed()->find($id) ?? new Lead();
        }
    }

    protected function deleteRedundantReflection(User $user, ?UserTypesEnum $previous_type): void
    {
        if ($previous_type != null && $user->user_type != $previous_type) {
            $reflection_model = $this->fetchReflectionModel($previous_type, $user->id);

            if ($reflection_model) {
                $reflection_model->delete();
            }
        }
    }
}
