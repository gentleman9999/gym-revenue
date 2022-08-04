<?php

namespace App\Domain\Templates\SmsTemplates\Events;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\StorableEvents\EntityCreated;

class SmsTemplateCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return SmsTemplate::class;
    }
}
