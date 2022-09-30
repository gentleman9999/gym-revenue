<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DynamicReport extends GymRevProjection
{
    use HasFactory;

    protected $fillable = ['id', 'client_id', 'name', 'model', 'filters'];
}
