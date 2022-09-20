<?php

namespace App\Domain\Clients\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $gateway_id
 * @property string $client_id
 * @property string $nickname
 * @property bool $active
 */
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
        'client_id',
        'nickname',
        'active',
    ];
}
