<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Events;

use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\StorableEvents\EntityTrashed;

class CallScriptTemplateTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return CallScriptTemplate::class;
    }
}
