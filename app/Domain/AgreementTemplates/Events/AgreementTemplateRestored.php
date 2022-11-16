<?php

namespace App\Domain\AgreementTemplates\Events;

use App\Domain\Agreements\Projections\AgreementTemplate;
use App\StorableEvents\EntityRestored;

class AgreementTemplateRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return AgreementTemplate::class;
    }
}
