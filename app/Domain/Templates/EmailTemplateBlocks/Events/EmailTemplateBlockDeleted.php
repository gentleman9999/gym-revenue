<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Events;

use App\Domain\Templates\EmailTemplateBlocks\Models\EmailTemplateBlock;
use App\StorableEvents\EntityDeleted;

/**
 * @property array $payload
 */
class EmailTemplateBlockDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return EmailTemplateBlock::class;
    }
}
