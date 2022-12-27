<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests\Events;

use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use App\StorableEvents\EntityCreated;

class StructuredDocumentRequestCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return StructuredDocumentRequest::class;
    }
}
