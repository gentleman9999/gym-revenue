<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\Events;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\StorableEvents\EntityDeleted;

class StructuredDocumentDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return StructuredDocument::class;
    }
}
