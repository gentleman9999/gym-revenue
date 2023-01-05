<?php

namespace App\Domain\Templates\EmailTemplates\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class CreateEmailTemplateInputValidator extends Validator
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
                'string',
                'required',
                'min:5',
                'max:50',
            ],
            'subject' => [
                'string',
                'required',
                'min:4',
                'max:128',
            ],
            'json' => [
                'string',
                'required',
            ],
            'markup' => [
                'string',
                'required',
            ],
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
            /* name */
            'name.required' => 'You must name your email template.',
            'name.min' => 'why is your name so small?',
            'name.max' => 'why is your name so big?',
            /* subject */
            'subject.required' => 'Cant\'t send an email without a subject',
            'subject.max' => 'who sends emails with such long titles?',
            /* json */
            'json.required' => 'json must be provided',
            /* markup */
            'markup.required' => 'markup must be provided',
        ];
    }
}
