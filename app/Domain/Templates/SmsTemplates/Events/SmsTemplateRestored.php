<?php

declare(strict_types=1);

namespace App\Domain\Templates\SmsTemplates\Events;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\StorableEvents\EntityRestored;

class SmsTemplateRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return SmsTemplate::class;
    }
}
