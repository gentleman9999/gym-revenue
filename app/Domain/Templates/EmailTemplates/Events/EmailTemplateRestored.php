<?php

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
