<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests\Events;

use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\StorableEvents\EntityRestored;

class StructuredDocumentRequestRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return StructuredDocumentRequest::class;
    }
}
