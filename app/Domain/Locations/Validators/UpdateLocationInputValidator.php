<?php

declare(strict_types=1);

namespace App\Domain\Locations\Validators;

use App\Domain\Locations\Enums\LocationTypes;
use Illuminate\Validation\Rules\Enum;
use Nuwave\Lighthouse\Validation\Validator;

final class UpdateLocationInputValidator extends Validator
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
                'required',
            ],
            'gymrevenue_id' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'location_no' => [
                'sometimes',
                'string',
                'max:10',
            ],
            'location_type' => [new Enum(LocationTypes::class)],
            'name' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'city' => [
                'sometimes',
                'string',
                'max:30',
            ],
            'state' => [
                'sometimes',
                'string',
                'size:2',
            ],
            'active' => [
                'boolean',
                'sometimes',
            ],
            'zip' => [
                'sometimes',
                'nullable',
                'string',
                'size:5',
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'size:10',
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
            'poc_phone' => [
                'sometimes',
                'nullable',
                'string',
                'size:10',
            ],
            'poc_first' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],
            'poc_last' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],
            'opened_at' => [
                'sometimes',
                'nullable',
                'date',
                'before:close_date',
            ],
            'closed_at' => [
                'sometimes',
                'nullable',
                'date',
                'after:open_date',
            ],
            'latitude' => [
                'sometimes',
                'nullable',
                'int',
            ],
            'longitude' => [
                'sometimes',
                'nullable',
                'int',
            ],
            'capacity' => [
                'sometimes',
                'int',
                'min:0',
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
            'gymrevenue_id' => 'GymRevenue ID',
            'location_no' => 'Location Number',
            'location_type' => 'Location Type',
            'name' => 'Name',
            'city' => 'City',
            'state' => 'State',
            'active' => 'Active',
            'zip' => 'Zip Code',
            'address1' => 'Address',
            'address2' => 'Address 2',
            'poc_phone' => 'POC Phone',
            'poc_first' => 'POC First',
            'poc_last' => 'POC Last',
            'opened_at' => 'Open Date',
            'closed_at' => 'Close Date',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'capacity' => 'Capacity',
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
            'name' => [
                'string' => ':attribute must be a string.',
            ],
            'location_type' => ':attribute must be one of the following: :values',
            '*.max' => ':attribute must not be more than :max characters.',
            '*.size' => ':attribute must be exactly :size characters long.',
            '*.in' => ':attribute must be one of the following: :values',
        ];
    }
}
