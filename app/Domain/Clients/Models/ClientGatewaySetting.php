<?php

namespace App\Domain\Clients\Models;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientGatewaySetting extends GymRevProjection
{
    use HasFactory;

    protected $fillable = [
        'gateway_provider',
        'name',
        'value',
        'client_id',
    ];

    protected $hidden = ['client_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
