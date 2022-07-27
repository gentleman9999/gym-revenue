<?php

namespace App\Domain\Templates\EmailTemplates\Events;

use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\StorableEvents\EntityUpdated;

class EmailTemplateUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return EmailTemplate::class;
    }
}
