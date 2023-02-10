<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ObfuscatedUser extends GymRevProjection
{
    use HasFactory;

    /** @var string */
    protected $table = 'obfuscated_users';

    /** @var array<string> */
    protected $fillable = [
        'client_id',
        'user_id',
        'email',
        'data',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'data' => 'array',
    ];
}
