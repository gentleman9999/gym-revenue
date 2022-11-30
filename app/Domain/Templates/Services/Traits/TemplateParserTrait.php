<?php

declare(strict_types=1);

namespace App\Domain\Templates\Services\Traits;

use App\Domain\Templates\Services\TemplateParserService;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Model
 */
trait TemplateParserTrait
{
    /**
     * @inheritDoc
     */
    public function getMarkupAttribute(string $value): string
    {
        return base64_decode($value);
    }

    /**
     * @inheritDoc
     */
    public function parseMarkup(array $data = []): string
    {
        return (new TemplateParserService($this->markup))->swapTokens($data);
    }
}
