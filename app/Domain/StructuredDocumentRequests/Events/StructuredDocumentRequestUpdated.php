<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests\Events;

use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\StorableEvents\EntityUpdated;

class StructuredDocumentRequestUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return StructuredDocumentRequest::class;
    }
}
