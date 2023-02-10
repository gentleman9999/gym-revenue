<?php

declare(strict_types=1);

namespace App\Domain\Positions\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdatePositionInputValidator extends Validator
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
                'string',
            ],
            'name' => [
                'required',
                'string',
            ],
            'departments' => [
                'sometimes',
                'array',
            ],
            'departments.*' => [
                'sometimes',
                'nullable',
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
            'name' => 'Name',
            'departments' => 'Departments',
        ];
    }
}
