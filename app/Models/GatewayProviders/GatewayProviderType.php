<?php

declare(strict_types=1);

namespace App\Models\GatewayProviders;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class GatewayProviderType extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $fillable = [
        'name',
        'slug',
        'desc',
        'active',
        'misc',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'misc' => 'array',
    ];
}
