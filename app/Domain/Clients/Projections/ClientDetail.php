<?php

namespace App\Domain\Clients\Projections;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientDetail extends GymRevDetailProjection
{
    use SoftDeletes;

    protected $casts = [
        'misc' => 'array',
    ];

    public static function getRelatedModel()
    {
        return new Client();
    }

    public static function fk(): string
    {
        return "client_id";
    }
}
