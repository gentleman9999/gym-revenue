<?php

namespace App\Models;

use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientCommunicationPreference extends GymRevProjection
{
    use HasFactory;

    protected $fillable = ['sms', 'email'];

    protected $hidden = ['client_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
