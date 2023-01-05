<?php

namespace App\Domain\Files\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateFileFolderInputValidator extends Validator
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
            'folder' => [
                'required',
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
            'folder' => 'Folder ID',
        ];
    }
}
