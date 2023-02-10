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

    /** @var array<string> */
    protected $hidden = ['client_id'];
    /** @var string */
    protected $primaryKey = 'id';
    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $fillable = [
        'name',
        'value',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
