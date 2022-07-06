<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'name'];

    protected $keyType = 'string';

    public $incrementing = false;

    public function department()
    {
        return $this->belongsToMany(Department::class, 'department_position', 'position_id', 'department_id');
    }
}
