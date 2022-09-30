<?php

declare(strict_types=1);

namespace App\Domain\Templates\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class TemplateParser
{
    use AsAction;

    public const TYPE_EVAL = 'eval';
    public const TYPE_MAIL_GUN = 'mail-gun';

    private const CONSTRAINTS_NAMES = [
        'First Name' => '{{first_name}}',
        'Last Name' => '{{last_name}}',
        'Date Of Birth' => '{{dob}}',
        'Home Club' => '{{home_club}}',
        'Created At' => '{{created_at}}',
    ];

    private const CONSTRAINTS_MAPS = [
        '{{first_name}}' => [
            self::TYPE_MAIL_GUN => '{{ recipient.first_name }}',
            self::TYPE_EVAL => 'user.first_name',
        ],
        '{{last_name}}' => [
            self::TYPE_MAIL_GUN => '{{ recipient.last_name }}',
            self::TYPE_EVAL => 'user.last_name',
        ],
        '{{dob}}' => [
            self::TYPE_MAIL_GUN => '{{ recipient.date_of_birth }}',
            self::TYPE_EVAL => 'user.date_of_birth',
        ],
        '{{home_club}}' => [
            self::TYPE_MAIL_GUN => '{{recipient.home_club}}',
            self::TYPE_EVAL => 'user.home_club',
        ],
        '{{created_at}}' => [
            self::TYPE_MAIL_GUN => '{{recipient.created_at}}',
            self::TYPE_EVAL => 'user.created_at',
        ],
    ];

    private function parse(string $token, array $data): mixed
    {
        if (empty($data)) {
            return $token;
        }

        return Arr::get($data, $token) ?: $token;
    }

    public function handle(string $markup, array $data = [], string $type = self::TYPE_EVAL): string
    {
        $matches = [];
        $replacements = [];

        foreach (self::CONSTRAINTS_MAPS as $constraint => $types) {
            $matches[] = $constraint;
            $replacements[] = $this->parse($types[$type], $data);
        }

        return str_replace($matches, $replacements, $markup);
    }

    public function asController(ActionRequest $request): array
    {
        return self::CONSTRAINTS_NAMES;
    }

    public function jsonResponse(array $constraints): JsonResponse
    {
        return new JsonResponse($constraints);
    }
}
