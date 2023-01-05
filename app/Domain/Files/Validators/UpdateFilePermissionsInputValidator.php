<?php

namespace App\Domain\Files\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateFilePermissionsInputValidator extends Validator
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
            'permissions' => [
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
            'permissions' => 'Permissions',
        ];
    }
}
