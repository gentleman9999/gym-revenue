<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tasks extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $fillable = ['id', 'user_id', 'due_at', 'completed_at', 'title', 'description', 'created_at', 'updated_at', 'deleted_at'];
}
