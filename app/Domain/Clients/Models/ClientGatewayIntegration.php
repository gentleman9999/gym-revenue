<?php

declare(strict_types=1);

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

    /** @var bool  */
    public $incrementing = false;

    /** @var string  */
    protected $primaryKey = 'id';

    /** @var string  */
    protected $keyType = 'string';

    /** @var array<string>  */
    protected $fillable = [
        'gateway_id',
        'client_id',
        'nickname',
        'active',
    ];
}
