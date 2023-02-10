<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateEmailTemplateInputValidator extends Validator
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
                'sometimes',
                'nullable',
                'string',
                'min:5',
                'max:50',
            ],
            'subject' => [
                'sometimes',
                'nullable',
                'string',
                'min:4',
                'max:128',
            ],
            'json' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'markup' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }
}
