<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'name'];

    protected $keyType = 'string';

    public $incrementing = false;

    public function position()
    {
        return $this->belongsToMany(Position::class, 'department_position', 'department_id', 'position_id');
    }
}
