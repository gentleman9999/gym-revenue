<?php

declare(strict_types=1);

namespace App\Domain\Files\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class CreateFileInputValidator extends Validator
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
            'filename' => [
                'required',
                'string',
                'max:255',
            ],
            'original_filename' => [
                'required',
                'string',
                'max:255',
            ],
            'extension' => [
                'required',
                'string',
                'min:3',
                'max:4',
            ],
            'bucket' => [
                'required',
                'string',
                'max:255',
            ],
            'key' => [
                'required',
                'string',
                'max:255',
            ],
            'size' => [
                'required',
                'integer',
                'min:255',
            ],
            'visibility' => [
                'sometimes',
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
            'filename' => 'File Name',
            'original_filename' => 'File Original Name',
            'extension' => 'File Extension',
            'bucket' => 'AWS Bucket',
            'key' => 'AWS Key',
            'size' => 'File Size',
            'visibility' => 'File Visibility',
        ];
    }
}
