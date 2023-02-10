<?php

declare(strict_types=1);

namespace App\Domain\Users\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class NoteInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'note' => [
                'sometimes',
                'string',
            ],
            'title' => [
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
            'note' => 'Note',
            'title' => 'Title',
        ];
    }
}
