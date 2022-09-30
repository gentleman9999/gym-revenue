<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks\Events;

use App\Domain\Templates\EmailTemplateBlocks\Models\EmailTemplateBlock;
use App\StorableEvents\EntityCreated;

class EmailTemplateBlockCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return EmailTemplateBlock::class;
    }
}
