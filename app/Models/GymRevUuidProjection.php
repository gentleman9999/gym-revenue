<?php

declare(strict_types=1);

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

abstract class GymRevUuidProjection extends GymRevProjection
{
    use Uuid;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $keyType = 'string';
}
