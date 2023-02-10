<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Events;

use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\StorableEvents\EntityRestored;

class CallScriptTemplateRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return CallScriptTemplate::class;
    }
}
