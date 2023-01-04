<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\StructuredDocumentFiles\Events;

use App\Domain\StructuredDocuments\StructuredDocumentFiles\Projections\StructuredDocumentFile;
use App\StorableEvents\EntityTrashed;

class StructuredDocumentFileTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return StructuredDocumentFile::class;
    }
}
