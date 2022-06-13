<?php

namespace App\Domain\Clients\Projections;

use App\Domain\Clients\Models\Client;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EventSourcing\Projections\Projection;

class ClientActivity extends Projection
{
    public $timestamps = false;

    protected $hidden = ['access_token'];

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
