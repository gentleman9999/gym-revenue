<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\Events;

use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use App\StorableEvents\EntityTrashed;

class StructuredDocumentTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return StructuredDocument::class;
    }
}
