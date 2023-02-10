<?php

declare(strict_types=1);

namespace App\Domain\Templates\SmsTemplates\Events;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\StorableEvents\EntityUpdated;

class SmsTemplateUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return SmsTemplate::class;
    }
}
