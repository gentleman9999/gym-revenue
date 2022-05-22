<?php

namespace App\Models\GatewayProviders;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GatewayProviderDetail extends Model
{
    use SoftDeletes;
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['gateway_id', 'detail', 'value', 'misc', 'active'];

    protected $casts = [
        'misc' => 'array',
    ];
}
