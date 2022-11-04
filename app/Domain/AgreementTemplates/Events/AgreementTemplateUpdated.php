<?php

namespace App\Domain\AgreementTemplates\Events;

use App\Domain\Agreements\Projections\AgreementTemplate;
use App\StorableEvents\EntityUpdated;

class AgreementTemplateUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return AgreementTemplate::class;
    }
}
