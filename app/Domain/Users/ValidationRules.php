<?php

namespace App\Domain\Users;

use App\Enums\StatesEnum;
use App\Enums\UserGenderEnum;
use App\Enums\UserTypesEnum;
use Illuminate\Validation\Rules\Enum;

class ValidationRules
{
    public static function getValidationRules(string $user_type, bool $is_create_mode): array
    {
        $validation_req = $is_create_mode ? 'required' : 'sometimes';

        switch ($user_type) {
            case UserTypesEnum::EMPLOYEE:
                return self::getEmployeeUserRules($validation_req);
            case UserTypesEnum::CUSTOMER:
                return self::getCustomerUserRules($validation_req);
            case UserTypesEnum::MEMBER:
                return self::getMemberUserRules($validation_req);
            default:
                return self::getLeadUserRules($validation_req);
        }
    }

    /**
     * Validation Rules which applies for all user types
     *
     * @param string $validation_req 'required' or 'sometimes'
     *
     * @return array
     */
    private static function getbaseValidationRules(string $validation_req): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'email' => [$validation_req, 'email:rfc,dns'],
            'first_name' => [$validation_req, 'max:50'],
            'middle_name' => [],
            'last_name' => [$validation_req, 'max:30'],
            'phone' => [$validation_req, 'min:10'],
            'user_type' => [$validation_req, new Enum(UserTypesEnum::class)],
            'gender' => [$validation_req, new Enum(UserGenderEnum::class)],
            'unsubscribed_email' => ['sometimes', 'boolean'],
            'unsubscribed_sms' => ['sometimes', 'boolean'],
            'home_location_id' => ['sometimes', 'exists:locations,gymrevenue_id'],
            'address2' => [],
            'alternate_phone' => ['nullable', 'string', 'min:10'],
        ];
    }

    /**
     * Validation Rules which applies for employee type users
     *
     * @param string $validation_req 'required' or 'sometimes'
     *
     * @return array
     */
    private static function getEmployeeUserRules(string $validation_req): array
    {
        $rules = self::getbaseValidationRules($validation_req);
        $rules['gender'] = [$validation_req, new Enum(UserGenderEnum::class)];
        $rules['address1'] = [$validation_req];
        $rules['zip'] = [$validation_req, 'size:5'];
        $rules['city'] = [$validation_req, 'max:30'];
        $rules['state'] = [$validation_req, 'size:2', new Enum(StatesEnum::class)];

        return $rules;
    }

    /**
     * Validation Rules which applies for employee type users
     *
     * @param string $validation_req 'required' or 'sometimes'
     *
     * @return array
     */
    private static function getMemberUserRules(string $validation_req): array
    {
        $rules = self::getbaseValidationRules($validation_req);
        $rules['date_of_birth'] = [$validation_req, 'date_format:Y-m-d H:i:s'];
        $rules['address1'] = [$validation_req];
        $rules['zip'] = [$validation_req, 'size:5'];
        $rules['city'] = [$validation_req, 'max:30'];
        $rules['state'] = [$validation_req, 'size:2', new Enum(StatesEnum::class)];

        return $rules;
    }

    /**
     * Validation Rules which applies for customer type users
     *
     * @param string $validation_req 'required' or 'sometimes'
     *
     * @return array
     */
    private static function getCustomerUserRules(string $validation_req): array
    {
        $rules = self::getbaseValidationRules($validation_req);
        $rules['date_of_birth'] = [$validation_req, 'date_format:Y-m-d H:i:s'];
        $rules['address1'] = [$validation_req];
        $rules['zip'] = [$validation_req, 'size:5'];
        $rules['city'] = [$validation_req, 'max:30'];
        $rules['state'] = [$validation_req, 'size:2', new Enum(StatesEnum::class)];

        return $rules;
    }

    /**
     * Validation Rules which applies for lead type users
     *
     * @param string $validation_req 'required' or 'sometimes'
     *
     * @return array
     */
    private static function getLeadUserRules(string $validation_req): array
    {
        $rules = self::getbaseValidationRules($validation_req);
        $rules['entry_source'] = [$validation_req, 'json'];
        $rules['opportunity'] = [$validation_req, 'integer', 'in:0,1,2,3'];
        $rules['alternate_phone'] = ['sometimes', 'nullable', 'string', 'min:10'];
        $rules['date_of_birth'] = ['sometimes', 'nullable', 'date_format:Y-m-d H:i:s'];
        $rules['owner_user_id'] = ['sometimes', 'nullable', 'exists:users,id'];

        return $rules;
    }
}
