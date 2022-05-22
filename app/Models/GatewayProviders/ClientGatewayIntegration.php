<?php

namespace App\Models\GatewayProviders;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ClientGatewayIntegration extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'gateway_id',
        'gateway_slug',
        'client_id',
        'provider_type',
        'nickname',
        'active',
    ];
}
