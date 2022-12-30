<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ObfuscatedUser extends GymRevProjection
{
    use HasFactory;

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'obfuscated_users';

    protected $fillable = [
        'client_id',
        'user_id',
        'email',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
