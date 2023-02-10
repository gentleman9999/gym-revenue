<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Events;

use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\StorableEvents\EntityRestored;

class EmailTemplateRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return EmailTemplate::class;
    }
}
