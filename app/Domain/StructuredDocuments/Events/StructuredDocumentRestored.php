<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\Events;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\StorableEvents\EntityRestored;

class StructuredDocumentRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return StructuredDocument::class;
    }
}
