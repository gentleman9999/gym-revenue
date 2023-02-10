<?php

declare(strict_types=1);

namespace App\Domain\Notes\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateNoteInputValidator extends Validator
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
            'title' => [
                'required',
                'string',
            ],
            'entity_id' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'entity_type' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'note' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'active' => [
                'sometimes',
                'nullable',
                'boolean',
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
            'title' => 'Note Title',
            'entity_id' => 'Note Entity ID',
            'entity_type' => 'Note Entity Type',
            'note' => 'Note Content',
            'active' => 'Note Active',
        ];
    }
}
