<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObfuscatedUsers extends Model
{
    use HasFactory;

    /** @var string  */
    protected $table = 'obfuscated_users';
}
