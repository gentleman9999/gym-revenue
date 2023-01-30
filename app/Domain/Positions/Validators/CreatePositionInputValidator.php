<?php

namespace App\Domain\Positions\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class CreatePositionInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
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
            'name' => 'Name',
            'departments' => 'Departments',
        ];
    }
}
