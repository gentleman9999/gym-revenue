<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = ['id', 'entity_type', 'entity_id', 'user_id', 'name', 'description', 'remind_time'];
}
