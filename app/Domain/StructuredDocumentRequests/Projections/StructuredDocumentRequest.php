<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests\Projections;

use App\Enums\StructuredDocumentEntityTypeEnum;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Database\Factories\StructuredDocumentRequestFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StructuredDocumentRequest extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['client_id', 'structured_document_id', 'entity_id', 'entity_type'];

    protected $casts = [
        'entity_type' => StructuredDocumentEntityTypeEnum::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    protected static function newFactory(): Factory
    {
        return StructuredDocumentRequestFactory::new();
    }
}
