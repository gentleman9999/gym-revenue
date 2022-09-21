<?php

namespace App\Domain\Clients\Projections;

use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientActivity extends GymRevProjection
{
    public $timestamps = false;

    protected $hidden = ['access_token', 'client_id'];

    protected $guarded = [];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function getKeyName(): string
    {
        return 'stored_event_id';
    }

    public function getRouteKeyName(): string
    {
        return 'stored_event_id';
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withoutGlobalScopes();
    }

    public function entityObject(): ?HasOne
    {
        if ($this->entity) {
            return $this->hasOne(new $this->entity(), 'id', 'entity_id');
        }

        return null;
    }
}
