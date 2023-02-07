<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntrySourceCategory extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = ['client_id'];
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'value',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
