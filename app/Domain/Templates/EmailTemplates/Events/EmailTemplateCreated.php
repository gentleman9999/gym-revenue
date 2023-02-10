<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Events;

use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\StorableEvents\EntityCreated;

class EmailTemplateCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return EmailTemplate::class;
    }
}
