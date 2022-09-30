<?php

namespace App\Domain\Templates\CallScriptTemplates\Events;

use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\StorableEvents\EntityUpdated;

class CallScriptTemplateUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return CallScriptTemplate::class;
    }
}
