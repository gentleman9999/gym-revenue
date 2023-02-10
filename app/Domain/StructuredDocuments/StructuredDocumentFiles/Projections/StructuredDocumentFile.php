<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\StructuredDocumentFiles\Projections;

use App\Domain\Clients\Projections\Client;
use App\Enums\StructuredDocumentEntityTypeEnum;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use CapeAndBay\Versionable\Versionable;
use Database\Factories\StructuredDocumentFileFactory;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class StructuredDocumentFile extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    /** @see https://github.com/overtrue/laravel-versionable */
    use Versionable;
    use Uuid;

    /** @var array<string> */
    protected $fillable = ['client_id', 'file_id', 'entity_type', 'entity_id', 'structured_document_id', 'expires_at'];

    /** @var array<string, string> */
    protected $casts = [
        'entity_type' => StructuredDocumentEntityTypeEnum::class,
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    protected static function newFactory(): Factory
    {
        return StructuredDocumentFileFactory::new();
    }
}
