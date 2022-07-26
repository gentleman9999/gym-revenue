<?php

namespace App\Domain\Templates\EmailTemplates\Events;

use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\StorableEvents\EntityDeleted;

class EmailTemplateDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return EmailTemplate::class;
    }
}
