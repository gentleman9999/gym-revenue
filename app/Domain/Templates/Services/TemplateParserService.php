<?php

declare(strict_types=1);

namespace App\Domain\Templates\Services;

use Illuminate\Support\Arr;

class TemplateParserService
{
    public const CONSTRAINTS_NAMES = [
        'First Name' => '{{user.first_name}}',
        'Last Name' => '{{user.last_name}}',
        'Date Of Birth' => '{{user.date_of_birth}}',
        'Home Club' => '{{user.home_club}}',
        'Created At' => '{{user.created_at}}',
    ];

    protected ?array $matches_cache = null;

    public function __construct(protected readonly string $markup)
    {
    }

    public function getReplacedToken(array $named_data): array
    {
        // For repeated calls, we don't want to parse the markup multiple times.
        if ($this->matches_cache === null) {
            preg_match_all('~{{\s*(.*?)\s*}}~', $this->markup, $this->matches_cache);
        }

        $replacements = [];
        foreach ($this->matches_cache[1] as $token) {
            $replacements[$token] = Arr::get($named_data, $token, '');
        }

        return $replacements;
    }

    public function clearMarkupCache(): static
    {
        $this->matches_cache = null;

        return $this;
    }

    public function swapTokens(array $data): string
    {
        $swaps = $this->getReplacedToken($data);

        return str_replace($this->matches_cache[0], $swaps, $this->markup);
    }
}
