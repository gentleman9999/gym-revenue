<?php

declare(strict_types=1);

namespace App\Domain\EntrySources;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntrySource extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = ['name', 'source', 'ui', 'active', 'misc', 'is_default_entry_source'];

    protected $casts = [
        'misc' => 'array',
    ];

    protected $hidden = ['client_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
