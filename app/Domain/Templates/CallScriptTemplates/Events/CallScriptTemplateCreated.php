<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Events;

use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\StorableEvents\EntityCreated;

class CallScriptTemplateCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return CallScriptTemplate::class;
    }
}
