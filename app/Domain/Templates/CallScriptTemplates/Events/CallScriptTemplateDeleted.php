<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Events;

use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\StorableEvents\EntityDeleted;

class CallScriptTemplateDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return CallScriptTemplate::class;
    }
}
