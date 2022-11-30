<?php

declare(strict_types=1);

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

abstract class GymRevUuidProjection extends GymRevProjection
{
    use Uuid;
    protected $keyType = 'string';

    public $incrementing = false;
}
