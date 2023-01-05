<?php

namespace App\Domain\Users\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UserInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'sometimes',
                'string',
            ],
            'first_name' => [
                'sometimes',
                'string',
            ],
            'last_name' => [
                'sometimes',
                'string',
            ],
            'email' => [
                'sometimes',
                'string',
            ],
            'alternate_email' => [
                'sometimes',
                'string',
            ],
            'address1' => [
                'sometimes',
                'string',
                'max:200',
            ],
            'address2' => [
                'sometimes',
                'nullable',
                'string',
                'max:200',
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'size:10',
            ],
            'city' => [
                'required',
                'string',
                'max:30',
            ],
            'state' => [
                'required',
                'string',
                'size:2',
            ],
            'zip' => [
                'sometimes',
                'nullable',
                'string',
                'size:5',
            ],
            'contact_preference' => [
                'sometimes',
                'string',
            ],
            'start_date' => [
                'required_if:end_date,1',
                'nullable',
                'date',
                'before:end_date',
            ],
            'end_date' => [
                'sometimes',
                'nullable',
                'date',
                'after:start_date',
            ],
            'termination_date' => [
                'sometimes',
                'nullable',
                'date',
                'after:start_date',
            ],
            'client_id' => [
                'sometimes',
                'string',
            ],
            'team_id' => [
                'sometimes',
                'string',
            ],
            'role_id' => [
                'sometimes',
                'string',
            ],
            'home_location_id' => [
                'sometimes',
                'string',
            ],
            'manager' => [
                'sometimes',
                'string',
            ],
            'departments' => [
                'sometimes',
                'array',
            ],
            'departments.*' => [
                'sometimes',
                'string',
            ],
            'positions' => [
                'sometimes',
                'array',
            ],
            'positions.*' => [
                'sometimes',
                'string',
            ],

        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email Address',
            'alternate_email' => 'Alternative Email Address',
            'address1' => 'Address',
            'address2' => 'Address 2',
            'phone' => 'Phone Number',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip Code',
            'contact_preference' => 'Contact Preference',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'termination_date' => 'Termination Date',
            'client_id' => 'Client ID',
            'team_id' => 'Team ID',
            'role_id' => 'Role ID',
            'home_location_id' => 'Home Location ID',
            'manager' => 'Manager',
            'departments' => 'Departments',
            'positions' => 'Positions',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            '*.required' => ':attribute is a required field.',
            '*.before' => ':attribute must be before :date.',
            '*.after' => ':attribute must be after :date.',
            '*.max' => ':attribute must not be more than :max characters.',
            '*.size' => ':attribute must be exactly :size characters long.',
            '*.in' => ':attribute must be one of the following: :values.',
        ];
    }
}
