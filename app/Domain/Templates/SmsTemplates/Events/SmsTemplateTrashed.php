<?php

declare(strict_types=1);

namespace App\Domain\Templates\SmsTemplates\Events;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\StorableEvents\EntityTrashed;

class SmsTemplateTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return SmsTemplate::class;
    }
}
