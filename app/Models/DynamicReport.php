<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DynamicReport extends GymRevProjection
{
    use HasFactory;

    /** @var array<string>  */
    protected $fillable = ['id', 'client_id', 'name', 'model', 'filters'];
}
