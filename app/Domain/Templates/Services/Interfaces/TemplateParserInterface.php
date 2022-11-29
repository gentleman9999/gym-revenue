<?php

declare(strict_types=1);

namespace App\Domain\Templates\Services\Interfaces;

interface TemplateParserInterface
{
    /**
     * Mutates the value of markup.
     *
     * @param  string  $value
     * @return string
     */
    public function getMarkupAttribute(string $value): string;

    /**
     * Get markup with swapped token.
     *
     * @param array $data   Tokens.
     *
     * @return string
     */
    public function parseMarkup(array $data = []): string;
}
