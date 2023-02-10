<?php

declare(strict_types=1);

namespace App\Domain\Templates\Services\Interfaces;

interface TemplateParserInterface
{
    /**
     * Mutates the value of markup.
     *
     */
    public function getMarkupAttribute(string $value): string;

    /**
     * Get markup with swapped token.
     *
     * @param array<string, mixed> $data   Tokens.
     *
     */
    public function parseMarkup(array $data = []): string;
}
