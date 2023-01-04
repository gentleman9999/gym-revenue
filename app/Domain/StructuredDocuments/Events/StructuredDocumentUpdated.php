<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\Events;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\StorableEvents\EntityUpdated;

class StructuredDocumentUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return StructuredDocument::class;
    }
}
